<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LocaleMiddleware::class,
        ]);
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'role' => \App\Http\Middleware\RoleAccess::class,
            'stock.auditor.guard' => \App\Http\Middleware\StockAuditorGuard::class,
        ]);
        $middleware->append(\App\Http\Middleware\StockAuditorGuard::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Session expire / unauthenticated: always go to /entry (never /login)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return redirect()->route('entry');
        });

        // CSRF token mismatch (419 - session expired on POST): go to /entry for admin pages
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            // Keep public shop / receipt verification on the 419 page (it now links to /entry)
            if ($request->is('/', 'shop*', 'sales/receipts/*', 'sitemap.xml')) {
                return null;
            }

            return redirect()->route('entry');
        });
    })->create();

// Rate limit for rider GPS location updates (throttle: 1 request per 4 seconds, 45/minute)
RateLimiter::for('tracking-location', function (Request $request) {
    $key = $request->user()?->id ?? $request->ip();

    return [
        Limit::perMinute(45)->by($key),
        Limit::perSeconds(4, 1)->by($key),
    ];
});

// Rate limit for trip tracking reads / heavy operations
RateLimiter::for('tracking-api', function (Request $request) {
    return Limit::perMinute(120)->by($request->user()?->id ?? $request->ip());
});
