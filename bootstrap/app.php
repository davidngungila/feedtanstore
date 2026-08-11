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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
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
