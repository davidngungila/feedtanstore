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
        // Get custom date range from request
        $startDate = $request->input('start_date', now()->startOfDay()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());
        
        // Convert to Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        // Label format for charts
        $labelFormat = $start->diffInDays($end) > 30 ? 'M Y' : 'd M';
        
        // Filtered purchase data
        $filteredPO = PurchaseOrder::whereBetween('created_at', [$start, $end])->get();
        $filteredPOCount = $filteredPO->count();
        $filteredPOAmount = $filteredPO->sum('total');
        $filteredGRN = GoodsReceivedNote::whereBetween('created_at', [$start, $end])->count();
        $filteredPayments = SupplierPayment::whereBetween('created_at', [$start, $end])->sum('amount');

        // Top suppliers
        $topSuppliers = Supplier::select(
                'suppliers.id',
                'suppliers.name',
                DB::raw('SUM(purchase_orders.total) as total_spent'),
                DB::raw('COUNT(purchase_orders.id) as po_count')
            )
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->whereBetween('purchase_orders.created_at', [$start, $end])
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Purchase trend (per day in date range)
        $purchaseData = [];
        $labels = [];
        $days = $start->diffInDays($end) + 1;
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $purchaseData[] = PurchaseOrder::whereDate('created_at', $date)->sum('total');
            $labels[] = $start->copy()->addDays($i)->format($labelFormat);
        }

        // PO status breakdown
        $poStatusBreakdown = PurchaseOrder::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$start, $end])
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
            'startDate',
            'endDate'
        ));
    }
}
