<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Session expire / unauthenticated: always go to /entry
            // (never to /login - entry generates a fresh signed login URL)
            return route('entry');
        }
        
        return null;
    }
}
