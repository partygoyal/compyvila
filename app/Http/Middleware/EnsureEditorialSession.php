<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEditorialSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }
        $lastSeen = (int) $request->session()->get('admin_seen', time());
        if (time() - $lastSeen > 3600) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('flash', 'Your editorial session expired. Please sign in again.');
        }
        $request->session()->put('admin_seen', time());

        return $next($request);
    }
}
