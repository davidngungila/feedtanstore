<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures Stock Auditor can ONLY access stock verification endpoints.
 * Blocks access to sales, inventory balances, revenue, adjustments, etc.
 */
class StockAuditorGuard
{
    protected array $blockedPrefixes = [
        'api/inventory/current-stock',
        'api/inventory/balances',
        'api/inventory/adjustments',
        'api/sales',
        'api/revenue',
        'api/finance',
        'api/users',
        'api/settings',
        'api/customers',
        'api/products',
        'inventory',
        'reports',
        'finance',
        'purchasing',
        'system',
        'customers',
        'sales',
        'hr',
        'store',
        'dashboard',
        'revenue',
        'analytics',
        'online/orders',
    ];

    protected array $allowedForAuditor = [
        'stock-verification',
        'api/stock-verification',
        'logout',
        'login',
        'profile',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && $user->role === 'stock_auditor') {
            $path = trim($request->path(), '/');
            foreach ($this->blockedPrefixes as $blocked) {
                if ($path === $blocked || str_starts_with($path, $blocked)) {
                    if ($request->expectsJson()) return response()->json(['message'=>'Forbidden: stock auditors may only access stock verification'],403);
                    abort(403, 'Stock auditors may only access Stock Verification');
                }
            }
            // also block direct API access to system quantity via query param leakage
            if ($request->has('include_system_quantity') || $request->has('show_variance')) {
                if ($request->expectsJson()) return response()->json(['message'=>'Forbidden'],403);
                abort(403);
            }
        }
        if ($user && $user->role === 'external_auditor') {
            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
                // external auditors are read-only
                if (!$request->is('api/*') && !$request->expectsJson()) {
                    // allow GET only
                    abort(403, 'External auditors have read-only access');
                }
                if ($request->expectsJson()) return response()->json(['message'=>'Forbidden: read-only role'],403);
                abort(403);
            }
        }
        return $next($request);
    }
}
