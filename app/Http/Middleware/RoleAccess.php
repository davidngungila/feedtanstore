<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleAccess
{
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->expectsJson()) return response()->json(['message'=>'Unauthenticated'],401);
            return redirect()->route('login');
        }
        // admin always passes unless explicitly restricted? No - strict for stock_auditor isolation but admin can do everything
        if ($user->role === 'admin') return $next($request);
        if (!in_array($user->role, $allowedRoles, true)) {
            if ($request->expectsJson()) return response()->json(['message'=>'Forbidden: insufficient role'],403);
            abort(403, 'Unauthorized role: '.$user->role);
        }
        return $next($request);
    }
}
