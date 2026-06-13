<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchasesDashboardController extends Controller
{
    public function index()
    {
        // Today's purchase data
        $todayPO = PurchaseOrder::whereDate('created_at', today())->get();
        $todayPOCount = $todayPO->count();
        $todayPOAmount = $todayPO->sum('total');
        $todayGRN = GoodsReceivedNote::whereDate('created_at', today())->count();
        $todayPayments = SupplierPayment::whereDate('created_at', today())->sum('amount');

        // This month's data
        $thisMonthPO = PurchaseOrder::whereMonth('created_at', '>=', now()->startOfMonth())->get();
        $thisMonthPOCount = $thisMonthPO->count();
        $thisMonthPOAmount = $thisMonthPO->sum('total');
        $thisMonthGRN = GoodsReceivedNote::whereMonth('created_at', '>=', now()->startOfMonth())->count();
        $thisMonthPayments = SupplierPayment::whereMonth('created_at', '>=', now()->startOfMonth())->sum('amount');

        // Top suppliers this month
        $topSuppliers = Supplier::select(
                'suppliers.id',
                'suppliers.name',
                DB::raw('SUM(purchase_orders.total) as total_spent'),
                DB::raw('COUNT(purchase_orders.id) as po_count')
            )
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->where('purchase_orders.created_at', '>=', now()->startOfMonth())
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Purchase trend (last 30 days)
        $purchaseData = [];
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $purchaseData[] = PurchaseOrder::whereDate('created_at', $date)->sum('total');
            $labels[] = now()->subDays($i)->format('d M');
        }

        // PO status breakdown
        $poStatusBreakdown = PurchaseOrder::select('status', DB::raw('COUNT(*) as count'))->groupBy('status')->get();

        // Recent POs
        $recentPOs = PurchaseOrder::latest()->limit(10)->get();

        // Recent payments
        $recentPayments = SupplierPayment::latest()->limit(10)->get();

        return view('dashboard.purchases', compact(
            'todayPOCount',
            'todayPOAmount',
            'todayGRN',
            'todayPayments',
            'thisMonthPOCount',
            'thisMonthPOAmount',
            'thisMonthGRN',
            'thisMonthPayments',
            'topSuppliers',
            'purchaseData',
            'labels',
            'poStatusBreakdown',
            'recentPOs',
            'recentPayments'
        ));
    }
}
