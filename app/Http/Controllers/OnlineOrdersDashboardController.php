<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use App\Models\DeliveryRider;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OnlineOrdersDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get custom date range from request
        $startDate = $request->input('start_date', now()->startOfDay()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());
        
        // Convert to Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        // Label format for charts
        $labelFormat = $start->diffInDays($end) > 30 ? 'M Y' : 'd M';
        
        // Filtered online orders data
        $filteredOnlineOrders = OnlineOrder::whereBetween('created_at', [$start, $end])->get();
        $filteredOnlineRevenue = $filteredOnlineOrders->sum('total');
        $filteredOnlineOrdersCount = $filteredOnlineOrders->count();
        $filteredOnlineItems = OnlineOrderItem::whereHas('order', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })->sum('quantity');

        // Order status breakdown
        $statusBreakdown = OnlineOrder::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->get();

        // Top products sold online
        $topOnlineProducts = OnlineOrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(online_order_items.total) as total_amount')
            )
            ->whereHas('order', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product')
            ->get();

        // Rider performance
        $riderPerformance = DeliveryRider::withCount([
            'onlineOrders' => function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }
        ])->get();

        // Sales trend (per day in date range)
        $onlineSalesData = [];
        $labels = [];
        $days = $start->diffInDays($end) + 1;
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $onlineSalesData[] = OnlineOrder::whereDate('created_at', $date)->sum('total');
            $labels[] = $start->copy()->addDays($i)->format($labelFormat);
        }

        // Recent online orders
        $recentOnlineOrders = OnlineOrder::latest()->limit(10)->get();

        return view('dashboard.online-orders', compact(
            'filteredOnlineOrders',
            'filteredOnlineRevenue',
            'filteredOnlineOrdersCount',
            'filteredOnlineItems',
            'statusBreakdown',
            'topOnlineProducts',
            'riderPerformance',
            'onlineSalesData',
            'labels',
            'recentOnlineOrders',
            'startDate',
            'endDate'
        ));
    }
}
