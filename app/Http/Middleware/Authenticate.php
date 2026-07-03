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
            // Define public paths that should redirect to login instead of entry
            $publicPaths = [
                '/',
                '/shop',
                '/login',
                '/entry',
                '/sales/receipts/',
                '/api/shop/',
            ];
            
            // Get the intended URL or current path
            $path = $request->path();
            
            // Check if the path is public
            $isPublic = false;
            foreach ($publicPaths as $publicPath) {
                if (str_starts_with($path, trim($publicPath, '/')) || $path === trim($publicPath, '/')) {
                    $isPublic = true;
                    break;
                }
            }
            
            if ($isPublic) {
                // Public path, redirect to login
                return route('login');
            } else {
                // Protected path, redirect to entry
                return route('entry');
            }
        }
        
        return null;
    }
}
