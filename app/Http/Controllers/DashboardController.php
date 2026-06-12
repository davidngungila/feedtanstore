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
        
        // Get total data
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total');
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        
        // Get low stock products
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_level')->get();
        $lowStockCount = $lowStockProducts->count();
        
        // Get out of stock
        $outOfStockCount = Product::where('quantity', 0)->count();
        
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
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $daySale = Sale::whereDate('created_at', $date)->sum('total');
            $salesData[] = $daySale;
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
        
        // Get sales by category for pie chart
        $salesByCategory = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(sale_items.total) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->get();
        
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
            'outOfStockCount',
            'topProducts',
            'recentSales',
            'salesData',
            'labels',
            'paymentMethods',
            'cashierPerformance',
            'salesByCategory',
            'stockStatus'
        ));
    }
}