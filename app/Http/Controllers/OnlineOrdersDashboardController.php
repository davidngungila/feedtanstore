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
        // Get filter from request, default to 'day'
        $filter = $request->input('filter', 'day');
        
        // Calculate date range based on filter
        list($startDate, $endDate, $labelFormat) = $this->getDateRange($filter);
        
        // Filtered online orders data
        $filteredOnlineOrders = OnlineOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        $filteredOnlineRevenue = $filteredOnlineOrders->sum('total');
        $filteredOnlineOrdersCount = $filteredOnlineOrders->count();
        $filteredOnlineItems = OnlineOrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('quantity');

        // Order status breakdown
        $statusBreakdown = OnlineOrder::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Top products sold online
        $topOnlineProducts = OnlineOrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(online_order_items.total) as total_amount')
            )
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product')
            ->get();

        // Rider performance
        $riderPerformance = DeliveryRider::withCount([
            'onlineOrders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])->get();

        // Sales trend
        $onlineSalesData = [];
        $labels = [];
        $days = $this->getTrendDays($filter);
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $onlineSalesData[] = OnlineOrder::whereDate('created_at', $date)->sum('total');
            $labels[] = now()->subDays($i)->format($labelFormat);
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
            'filter'
        ));
    }

    private function getDateRange($filter)
    {
        $now = now();
        
        switch ($filter) {
            case 'week':
                return [$now->startOfWeek(), $now->endOfWeek(), 'd M'];
            case 'month':
                return [$now->startOfMonth(), $now->endOfMonth(), 'd M'];
            case '3months':
                return [$now->subMonths(3)->startOfMonth(), $now->endOfMonth(), 'M Y'];
            case '6months':
                return [$now->subMonths(6)->startOfMonth(), $now->endOfMonth(), 'M Y'];
            case 'year':
                return [$now->startOfYear(), $now->endOfYear(), 'M Y'];
            default: // day
                return [$now->startOfDay(), $now->endOfDay(), 'H:i'];
        }
    }

    private function getTrendDays($filter)
    {
        switch ($filter) {
            case 'week':
                return 7;
            case 'month':
                return 30;
            case '3months':
                return 90;
            case '6months':
                return 180;
            case 'year':
                return 365;
            default: // day
                return 24;
        }
    }
}
