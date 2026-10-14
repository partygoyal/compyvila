<?php
use App\Http\Middleware\{EnsureEditorialSession, SecurityHeaders, SharePublicationViewData};
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};
use Illuminate\Http\Request;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->web(append: [SharePublicationViewData::class]);
        $middleware->alias(['editorial' => EnsureEditorialSession::class]);
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->trimStrings(except: ['current_password', 'new_password']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['current_password', 'password', 'password_confirmation', 'new_password']);
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->expectsJson());
    })->create();
