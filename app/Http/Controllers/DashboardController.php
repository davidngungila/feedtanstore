<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Shift;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get today's sales
        $todaySales = Sale::whereDate('created_at', today())->get();
        $todayRevenue = $todaySales->sum('total');

        // Get this month's sales
        $thisMonthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();
        $thisMonthRevenue = $thisMonthSales->sum('total');

        // Get total sales
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total');

        // Get low stock products
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_level')->get();
        $lowStockCount = $lowStockProducts->count();

        // Get active customers
        $activeCustomers = Customer::count();

        // Get top selling products
        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->with('product')
            ->get();

        // Get recent sales
        $recentSales = Sale::with(['customer', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        // Get sales data for chart (last 7 days)
        $salesData = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $daySale = Sale::whereDate('created_at', $date)->sum('total');
            $salesData[] = $daySale;
            $labels[] = now()->subDays($i)->format('D');
        }

        // Get payment methods distribution
        $paymentMethods = Sale::select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayRevenue',
            'thisMonthSales',
            'thisMonthRevenue',
            'totalSales',
            'totalRevenue',
            'lowStockProducts',
            'lowStockCount',
            'activeCustomers',
            'topProducts',
            'recentSales',
            'salesData',
            'labels',
            'paymentMethods'
        ));
    }
}
