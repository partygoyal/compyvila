<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'country_code' => ['required', 'regex:/^\+\d{1,4}$/'],
            'mobile' => ['required', 'digits_between:6,15'],
            'password' => ['required', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->assignRole('participant');

        UserProfile::create([
            'user_id' => $user->id,
            'mobile' => $this->formatMobile(
                $validated['country_code'],
                $validated['mobile']
            ),
            'country' => 'India',
        ]);

        $token = $user->createToken('compyvila')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user->load('profile'),
        ]);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
        } catch (\Throwable $exception) {
            report($exception);

            return redirect(
                $this->frontendUrl('/login?google_login=failed')
            );
        }

        $user = User::where('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'CompyVila User',
                'email' => $googleUser->getEmail(),
                'password' => Str::password(32),
            ]);

            $user->assignRole('participant');

            UserProfile::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'country' => 'India',
            ]);
        }

        if (! $user->google_id) {
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        }

        $token = $user->createToken('compyvila')->plainTextToken;

        return redirect(
            $this->frontendUrl('/auth/google/callback?token=' . urlencode($token))
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (! $user || ! Hash::check(
            $request->password,
            $user->password
        )) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('compyvila')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user->load('profile'),
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()
            ->load([
                'profile',
                'roles',
            ]);

        return response()->json(
            $this->userWithNormalizedProfile($user)
        );
    }

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'mobile' => ['nullable', 'digits_between:6,15'],
            'country_code' => ['nullable', 'regex:/^\+\d{1,4}$/'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],

            'country' => ['nullable'],
            'state' => ['nullable'],
            'city' => ['nullable'],

            'address' => ['nullable'],

            'instagram' => ['nullable', 'url'],
            'facebook' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'website' => ['nullable', 'url'],
        ]);

        $user = $request->user();

        if ($user->hasRole('judge')) {
            return response()->json([
                'success' => false,
                'message' => 'Judge profiles cannot be edited from the dashboard.',
            ], 403);
        }

        $profile = UserProfile::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $profile->update([
            'mobile' => $request->mobile
                ? $this->formatMobile(
                    $request->country_code ?: '+91',
                    $request->mobile
                )
                : null,
            'dob' => $request->date_of_birth,
            'gender' => $request->gender,

            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,

            'address' => $request->address,
            'instagram_url' => $request->instagram,
            'facebook_url' => $request->facebook,
            'youtube_url' => $request->youtube,
            'website_url' => $request->website,
        ]);

        return response()->json([
            'success' => true,
            'profile' => $this->normalizedProfile(
                $profile->fresh()
            ),
        ]);
    }

    private function userWithNormalizedProfile(User $user): array
    {
        return [
            ...$user->toArray(),
            'profile' => $this->normalizedProfile($user->profile),
            'roles' => $user->roles
                ->pluck('name')
                ->values(),
        ];
    }

    private function normalizedProfile(?UserProfile $profile): ?array
    {
        if (! $profile) {
            return null;
        }

        $dateOfBirth = $profile->dob;

        return [
            ...$profile->toArray(),
            'date_of_birth' => $dateOfBirth,
            'age' => $dateOfBirth
                ? Carbon::parse($dateOfBirth)->age
                : null,
            'bio' => null,
            'country_code' => $this->mobileCountryCode($profile->mobile),
            'mobile' => $this->mobileWithoutCountryCode($profile->mobile),
            'instagram' => $profile->instagram_url,
            'facebook' => $profile->facebook_url,
            'youtube' => $profile->youtube_url,
            'website' => $profile->website_url,
        ];
    }

    private function formatMobile(string $countryCode, string $mobile): string
    {
        return $countryCode . preg_replace('/\D+/', '', $mobile);
    }

    private function mobileCountryCode(?string $mobile): string
    {
        if (! $mobile || ! str_starts_with($mobile, '+')) {
            return '+91';
        }

        foreach (['+91', '+1', '+44', '+61', '+971', '+65'] as $code) {
            if (str_starts_with($mobile, $code)) {
                return $code;
            }
        }

        return '+91';
    }

    private function mobileWithoutCountryCode(?string $mobile): ?string
    {
        if (! $mobile) {
            return null;
        }

        $countryCode = $this->mobileCountryCode($mobile);

        if (str_starts_with($mobile, $countryCode)) {
            return substr($mobile, strlen($countryCode));
        }

        return $mobile;
    }

    private function frontendUrl(string $path): string
    {
        return rtrim(
            env('FRONTEND_URL', 'http://localhost:3000'),
            '/'
        ) . $path;
    }
}
