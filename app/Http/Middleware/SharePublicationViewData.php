<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, View};
use Symfony\Component\HttpFoundation\Response;

class SharePublicationViewData
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('up')) {
            View::share([
                'path' => '/'.trim($request->path(), '/'),
                'title' => 'Page not found',
                'description' => 'Independent thinking. A clearer view of crypto.',
                'article' => null,
                'demo' => setting('demo', '1') === '1',
                'marketLive' => (bool) config('market.key'),
                'logged' => Auth::guard('admin')->check(),
                'tab' => $request->query('tab', 'overview'),
            ]);
        }

        return $next($request);
    }
}
