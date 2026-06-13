<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use App\Models\DeliveryRider;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OnlineOrdersDashboardController extends Controller
{
    public function index()
    {
        // Today's online orders
        $todayOnlineOrders = OnlineOrder::whereDate('created_at', today())->get();
        $todayOnlineRevenue = $todayOnlineOrders->sum('total');
        $todayOnlineOrdersCount = $todayOnlineOrders->count();
        $todayOnlineItems = OnlineOrderItem::whereHas('order', function($q) {
            $q->whereDate('created_at', today());
        })->sum('quantity');

        // This month's online orders
        $thisMonthOnlineOrders = OnlineOrder::where('created_at', '>=', now()->startOfMonth())->get();
        $thisMonthOnlineRevenue = $thisMonthOnlineOrders->sum('total');
        $thisMonthOnlineOrdersCount = $thisMonthOnlineOrders->count();

        // Order status breakdown
        $statusBreakdown = OnlineOrder::select('status', DB::raw('COUNT(*) as count'))->groupBy('status')->get();

        // Top products sold online
        $topOnlineProducts = OnlineOrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(online_order_items.total) as total_amount')
            )
            ->whereHas('order', function($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product')
            ->get();

        // Rider performance
        $riderPerformance = DeliveryRider::withCount([
            'onlineOrders' => function($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            }
        ])->get();

        // Sales trend (last 30 days)
        $onlineSalesData = [];
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $onlineSalesData[] = OnlineOrder::whereDate('created_at', $date)->sum('total');
            $labels[] = now()->subDays($i)->format('d M');
        }

        // Recent online orders
        $recentOnlineOrders = OnlineOrder::latest()->limit(10)->get();

        return view('dashboard.online-orders', compact(
            'todayOnlineOrders',
            'todayOnlineRevenue',
            'todayOnlineOrdersCount',
            'todayOnlineItems',
            'thisMonthOnlineOrders',
            'thisMonthOnlineRevenue',
            'thisMonthOnlineOrdersCount',
            'statusBreakdown',
            'topOnlineProducts',
            'riderPerformance',
            'onlineSalesData',
            'labels',
            'recentOnlineOrders'
        ));
    }
}
