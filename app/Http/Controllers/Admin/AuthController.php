<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:190'], 'password' => ['required', 'string', 'max:1000']]);
        $key = 'admin-login:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Too many sign-in attempts. Try again in 15 minutes.'])->onlyInput('email');
        }
        if (! Auth::guard('admin')->attempt(['email' => strtolower(trim($data['email'])), 'password' => $data['password']])) {
            RateLimiter::hit($key, 900);

            return back()->withErrors(['email' => 'Email or password was not recognised.'])->onlyInput('email');
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        $request->session()->put('admin_seen', time());

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
