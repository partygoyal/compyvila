<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $analytics = config('analytics.enabled') && ! $request->is('admin', 'admin/*', 'media-kit/download');
        if ($analytics) {
            $request->attributes->set('analytics.nonce', base64_encode(random_bytes(24)));
        }
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        if (! $request->is('media-kit/download')) {
            $scripts = $analytics ? " https://www.googletagmanager.com 'nonce-".$request->attributes->get('analytics.nonce')."'" : '';
            $images = $analytics ? ' https://www.googletagmanager.com https://*.google-analytics.com' : '';
            $connections = $analytics ? ' https://www.googletagmanager.com https://*.google-analytics.com https://*.google.com' : '';
            $response->headers->set('Content-Security-Policy', "default-src 'self'; img-src 'self' data:".$images."; style-src 'self'; script-src 'self'".$scripts."; connect-src 'self'".$connections."; font-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");
        }
        if ($request->is('admin', 'admin/*')) {
            $response->headers->set('Cache-Control', 'no-store, private');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
