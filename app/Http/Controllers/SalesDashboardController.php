<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\SaleReturn;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesDashboardController extends Controller
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
        
        // Filtered sales data
        $filteredSales = Sale::whereBetween('created_at', [$start, $end])->get();
        $filteredRevenue = $filteredSales->sum('total');
        $filteredTransactions = $filteredSales->count();
        $filteredItems = SaleItem::whereHas('sale', function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })->sum('quantity');

        // Sales trend (per day in date range)
        $salesData = [];
        $labels = [];
        $days = $start->diffInDays($end) + 1;
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $salesData[] = Sale::whereDate('created_at', $date)->sum('total');
            $labels[] = $start->copy()->addDays($i)->format($labelFormat);
        }

        // Top products
        $topProducts = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(sale_items.total) as total_amount')
        )
            ->whereHas('sale', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product')
            ->get();

        // Top customers
        $topCustomers = Customer::select(
            'customers.id',
            'customers.name',
            DB::raw('SUM(sales.total) as total_spent'),
            DB::raw('COUNT(sales.id) as transactions')
        )
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Returns
        $filteredReturns = SaleReturn::whereBetween('created_at', [$start, $end])->get();
        $returnsCount = $filteredReturns->count();
        $returnsAmount = $filteredReturns->sum('total');

        // Payment methods
        $paymentMethods = Sale::select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('payment_method')
            ->get();

        // Sales by hour (only if date range is 1 day)
        $salesByHour = [];
        if ($days === 1) {
            for ($h = 0; $h < 24; $h++) {
                $hourStr = str_pad($h, 2, '0', STR_PAD_LEFT);
                $salesByHour[] = Sale::where(DB::raw("strftime('%H', created_at)"), '=', $hourStr)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
            }
        }

        // Discounts usage
        $discountsUsed = Discount::withCount(['sales' => function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        }])->orderByDesc('sales_count')->limit(10)->get();

        return view('dashboard.sales', compact(
            'filteredSales',
            'filteredRevenue',
            'filteredTransactions',
            'filteredItems',
            'salesData',
            'labels',
            'topProducts',
            'topCustomers',
            'returnsCount',
            'returnsAmount',
            'paymentMethods',
            'salesByHour',
            'discountsUsed',
            'startDate',
            'endDate'
        ));
    }
}
