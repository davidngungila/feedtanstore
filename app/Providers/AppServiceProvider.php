<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share notification counts with all views
        View::composer('*', function ($view) {
            $outOfStockCount = Product::where('quantity', 0)->count();
            $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')
                ->where('quantity', '>', 0)
                ->count();
            $expiringCount = Product::whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>=', now())
                ->count();
            $expiredCount = Product::whereNotNull('expiry_date')
                ->where('expiry_date', '<', now())
                ->count();
            
            $totalNotifications = $outOfStockCount + $lowStockCount + $expiringCount + $expiredCount;
            
            $view->with([
                'outOfStockCount' => $outOfStockCount,
                'lowStockCount' => $lowStockCount,
                'expiringCount' => $expiringCount,
                'expiredCount' => $expiredCount,
                'totalNotifications' => $totalNotifications,
            ]);
        });
    }
}
