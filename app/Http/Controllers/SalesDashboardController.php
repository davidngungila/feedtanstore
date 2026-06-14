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
        // Get filter from request, default to 'day'
        $filter = $request->input('filter', 'day');
        
        // Calculate date range based on filter
        list($startDate, $endDate, $labelFormat) = $this->getDateRange($filter);
        
        // Filtered sales data
        $filteredSales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();
        $filteredRevenue = $filteredSales->sum('total');
        $filteredTransactions = $filteredSales->count();
        $filteredItems = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('quantity');

        // Sales trend
        $salesData = [];
        $labels = [];
        $days = $this->getTrendDays($filter);
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesData[] = Sale::whereDate('created_at', $date)->sum('total');
            $labels[] = now()->subDays($i)->format($labelFormat);
        }

        // Top products
        $topProducts = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(sale_items.total) as total_amount')
        )
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
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
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Returns
        $filteredReturns = SaleReturn::whereBetween('created_at', [$startDate, $endDate])->get();
        $returnsCount = $filteredReturns->count();
        $returnsAmount = $filteredReturns->sum('total');

        // Payment methods
        $paymentMethods = Sale::select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // Sales by hour (for day filter only)
        $salesByHour = [];
        if ($filter === 'day') {
            for ($h = 0; $h < 24; $h++) {
                $hourStr = str_pad($h, 2, '0', STR_PAD_LEFT);
                $salesByHour[] = Sale::where(DB::raw("strftime('%H', created_at)"), '=', $hourStr)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
            }
        }

        // Discounts usage
        $discountsUsed = Discount::withCount(['sales' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
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
