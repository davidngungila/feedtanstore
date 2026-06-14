<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PurchasesDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter from request, default to 'day'
        $filter = $request->input('filter', 'day');
        
        // Calculate date range based on filter
        list($startDate, $endDate, $labelFormat) = $this->getDateRange($filter);
        
        // Filtered purchase data
        $filteredPO = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        $filteredPOCount = $filteredPO->count();
        $filteredPOAmount = $filteredPO->sum('total');
        $filteredGRN = GoodsReceivedNote::whereBetween('created_at', [$startDate, $endDate])->count();
        $filteredPayments = SupplierPayment::whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        // Top suppliers
        $topSuppliers = Supplier::select(
                'suppliers.id',
                'suppliers.name',
                DB::raw('SUM(purchase_orders.total) as total_spent'),
                DB::raw('COUNT(purchase_orders.id) as po_count')
            )
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->whereBetween('purchase_orders.created_at', [$startDate, $endDate])
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Purchase trend
        $purchaseData = [];
        $labels = [];
        $days = $this->getTrendDays($filter);
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $purchaseData[] = PurchaseOrder::whereDate('created_at', $date)->sum('total');
            $labels[] = now()->subDays($i)->format($labelFormat);
        }

        // PO status breakdown
        $poStatusBreakdown = PurchaseOrder::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Recent POs
        $recentPOs = PurchaseOrder::latest()->limit(10)->get();

        // Recent payments
        $recentPayments = SupplierPayment::latest()->limit(10)->get();

        return view('dashboard.purchases', compact(
            'filteredPOCount',
            'filteredPOAmount',
            'filteredGRN',
            'filteredPayments',
            'topSuppliers',
            'purchaseData',
            'labels',
            'poStatusBreakdown',
            'recentPOs',
            'recentPayments',
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
