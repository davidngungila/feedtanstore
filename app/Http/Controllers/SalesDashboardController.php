<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\SaleReturn;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesDashboardController extends Controller
{
    public function index()
    {
        // Today's data
        $todaySales = Sale::whereDate('created_at', today())->get();
        $todayRevenue = $todaySales->sum('total');
        $todayTransactions = $todaySales->count();
        $todayItems = SaleItem::whereHas('sale', function($q) {
            $q->whereDate('created_at', today());
        })->sum('quantity');

        // This month
        $thisMonthSales = Sale::where('created_at', '>=', now()->startOfMonth())->get();
        $thisMonthRevenue = $thisMonthSales->sum('total');
        $thisMonthTransactions = $thisMonthSales->count();
        $thisMonthItems = SaleItem::whereHas('sale', function($q) {
            $q->where('created_at', '>=', now()->startOfMonth());
        })->sum('quantity');

        // Sales trend (last 30 days)
        $salesData = [];
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesData[] = Sale::whereDate('created_at', $date)->sum('total');
            $labels[] = now()->subDays($i)->format('d M');
        }

        // Top products this month
        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_amount')
            )
            ->whereHas('sale', function($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product')
            ->get();

        // Top customers this month
        $topCustomers = Customer::select(
                'customers.id',
                'customers.name',
                DB::raw('SUM(sales.total) as total_spent'),
                DB::raw('COUNT(sales.id) as transactions')
            )
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.created_at', '>=', now()->startOfMonth())
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Returns this month
        $thisMonthReturns = SaleReturn::where('created_at', '>=', now()->startOfMonth())->get();
        $returnsCount = $thisMonthReturns->count();
        $returnsAmount = $thisMonthReturns->sum('total');

        // Payment methods this month
        $paymentMethods = Sale::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', now()->startOfMonth())
            ->groupBy('payment_method')
            ->get();

        // Sales by hour (today)
        $salesByHour = [];
        for ($h = 0; $h < 24; $h++) {
            $salesByHour[] = Sale::where(DB::raw('HOUR(created_at)'), '=', $h)->whereDate('created_at', today())->count();
        }

        // Discounts usage this month
        $discountsUsed = Discount::withCount(['sales' => function($q) {
            $q->where('created_at', '>=', now()->startOfMonth());
        }])->orderByDesc('sales_count')->limit(10)->get();

        return view('dashboard.sales', compact(
            'todaySales',
            'todayRevenue',
            'todayTransactions',
            'todayItems',
            'thisMonthSales',
            'thisMonthRevenue',
            'thisMonthTransactions',
            'thisMonthItems',
            'salesData',
            'labels',
            'topProducts',
            'topCustomers',
            'returnsCount',
            'returnsAmount',
            'paymentMethods',
            'salesByHour',
            'discountsUsed'
        ));
    }
}
