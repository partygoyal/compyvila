<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Notifications\CompetitionRegistrationConfirmed;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompetitionRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_id' => ['required', 'exists:competitions,id'],
            'rules_accepted' => ['accepted'],
        ]);

        $competition = Competition::withCount('registrations')
            ->findOrFail($validated['competition_id']);

        $eligibilityErrors = $this->eligibilityErrors(
            $competition,
            $request->user()
        );

        if ($eligibilityErrors !== []) {
            return response()->json([
                'success' => false,
                'message' => $eligibilityErrors[0],
                'errors' => $eligibilityErrors,
            ], 422);
        }

        $existing = CompetitionRegistration::where(
            'competition_id',
            $validated['competition_id']
        )
        ->where(
            'user_id',
            $request->user()->id
        )
        ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Already registered for this competition.',
            ], 422);
        }

        $nextId = CompetitionRegistration::max('id') + 1;

        $registration = CompetitionRegistration::create([
            'competition_id' => $validated['competition_id'],
            'user_id' => $request->user()->id,
            'registration_number' => 'CV' . date('Y') . str_pad(
                $nextId,
                5,
                '0',
                STR_PAD_LEFT
            ),
            'rules_accepted' => true,
            'status' => 'pending',
        ]);

        $registration->load('competition');
        $this->sendNotification(
            $request->user(),
            new CompetitionRegistrationConfirmed($registration)
        );

        return response()->json([
            'success' => true,
            'message' => 'Competition registration successful.',
            'registration' => $registration,
        ]);
    }

    private function eligibilityErrors(Competition $competition, $user): array
    {
        $errors = [];
        $profile = $user->profile;
        $now = Carbon::now();

        if ($competition->status !== 'published') {
            $errors[] = 'This competition is not open for registration.';
        }

        if ($competition->registration_start && $now->lt(
            Carbon::parse($competition->registration_start)
        )) {
            $errors[] = 'Registration has not started yet.';
        }

        if ($competition->registration_end && $now->gt(
            Carbon::parse($competition->registration_end)
        )) {
            $errors[] = 'Registration has ended.';
        }

        if (
            $competition->max_participants !== null
            && $competition->registrations_count >= $competition->max_participants
        ) {
            $errors[] = 'This competition is full.';
        }

        $age = $this->profileAge($profile);

        if (($competition->min_age !== null || $competition->max_age !== null) && $age === null) {
            $errors[] = 'Please complete your date of birth before registering.';
        }

        if ($age !== null && $competition->min_age !== null && $age < $competition->min_age) {
            $errors[] = "You must be at least {$competition->min_age} years old to register.";
        }

        if ($age !== null && $competition->max_age !== null && $age > $competition->max_age) {
            $errors[] = "You must be {$competition->max_age} years old or younger to register.";
        }

        if ($competition->gender !== 'all') {
            if (! $profile?->gender) {
                $errors[] = 'Please complete your gender before registering.';
            } elseif ($profile->gender !== $competition->gender) {
                $errors[] = 'Your profile does not match the gender eligibility for this competition.';
            }
        }

        foreach (['country', 'state', 'city'] as $field) {
            if (! $competition->{$field}) {
                continue;
            }

            if (! $profile?->{$field}) {
                $errors[] = 'Please complete your location before registering.';
                break;
            }

            if (strcasecmp($profile->{$field}, $competition->{$field}) !== 0) {
                $errors[] = 'Your profile location does not match this competition eligibility.';
                break;
            }
        }

        return $errors;
    }

    private function profileAge($profile): ?int
    {
        if (! $profile) {
            return null;
        }

        if ($profile->age !== null) {
            return (int) $profile->age;
        }

        if (! $profile->dob) {
            return null;
        }

        return Carbon::parse($profile->dob)->age;
    }

    private function sendNotification($user, $notification): void
    {
        try {
            $user->notify($notification);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
