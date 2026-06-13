<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Shift;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get today's data
        $todaySales = Sale::whereDate('created_at', today())->get();
        $todayRevenue = $todaySales->sum('total');
        $todayItems = SaleItem::whereHas('sale', function($q) {
            $q->whereDate('created_at', today());
        })->sum('quantity');

        // Get this month's data
        $thisMonthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();
        $thisMonthRevenue = $thisMonthSales->sum('total');

        // Target for gauge chart - let's use 10,000,000 TZS as monthly target
        $monthlyTarget = 10000000;
        $targetPercentage = min(100, round(($thisMonthRevenue / $monthlyTarget) * 100));

        // Get total data
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total');
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        // Get low stock products
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_level')->get();
        $lowStockCount = $lowStockProducts->count();

        // Get out of stock
        $outOfStockProducts = Product::where('quantity', 0)->get();
        $outOfStockCount = $outOfStockProducts->count();

        // Get expiring products (in next 30 days)
        $expiringProducts = Product::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->get();
        $expiringCount = $expiringProducts->count();

        // Get expired products
        $expiredProducts = Product::whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->get();
        $expiredCount = $expiredProducts->count();

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
            ->limit(10)
            ->get();

        // Get sales data for chart (last 7 days)
        $salesData = [];
        $itemsSoldData = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $daySale = Sale::whereDate('created_at', $date)->sum('total');
            $dayItems = SaleItem::whereHas('sale', function($q) use ($date) {
                $q->whereDate('created_at', $date);
            })->sum('quantity');
            $salesData[] = $daySale;
            $itemsSoldData[] = $dayItems;
            $labels[] = now()->subDays($i)->format('D');
        }

        // Get payment methods distribution
        $paymentMethods = Sale::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Get cashier performance
        $cashierPerformance = User::withCount(['sales' => function($q) {
            $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }])->withSum(['sales' => function($q) {
            $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }], 'total')->get();

        // Get sales by category for pie/treemap chart
        $salesByCategory = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(sale_items.total) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->get();
        $categoryTotalSales = $salesByCategory->sum('total');

        // Get stock status
        $stockStatus = [
            'in_stock' => Product::where('quantity', '>', DB::raw('reorder_level'))->count(),
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount
        ];

        return view('dashboard', compact(
            'todaySales',
            'todayRevenue',
            'todayItems',
            'thisMonthSales',
            'thisMonthRevenue',
            'totalSales',
            'totalRevenue',
            'totalProducts',
            'totalCustomers',
            'lowStockProducts',
            'lowStockCount',
            'outOfStockProducts',
            'outOfStockCount',
            'expiringProducts',
            'expiringCount',
            'expiredProducts',
            'expiredCount',
            'topProducts',
            'recentSales',
            'salesData',
            'itemsSoldData',
            'labels',
            'paymentMethods',
            'cashierPerformance',
            'salesByCategory',
            'categoryTotalSales',
            'stockStatus',
            'monthlyTarget',
            'targetPercentage'
        ));
    }
}