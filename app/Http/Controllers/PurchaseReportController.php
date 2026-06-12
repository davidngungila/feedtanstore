<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function index()
    {
        // Get stats
        $totalOrders = PurchaseOrder::count();
        $totalGrns = GoodsReceivedNote::count();
        $totalPayments = SupplierPayment::count();
        $totalAmount = PurchaseOrder::sum('total');
        $totalPaid = SupplierPayment::sum('amount');

        // Monthly data for charts - MySQL compatible
        $monthlyOrders = PurchaseOrder::selectRaw(
                "YEAR(created_at) as year, 
                 MONTH(created_at) as month, 
                 COUNT(*) as count, 
                 SUM(total) as total"
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->reverse();

        $monthlyPayments = SupplierPayment::selectRaw(
                "YEAR(created_at) as year, 
                 MONTH(created_at) as month, 
                 COUNT(*) as count, 
                 SUM(amount) as total"
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->reverse();

        // Top suppliers
        $topSuppliers = Supplier::withCount(['purchaseOrders', 'goodsReceivedNotes'])
            ->withSum('purchaseOrders', 'total')
            ->orderBy('purchase_orders_sum_total', 'desc')
            ->limit(10)
            ->get();

        // Recent orders
        $recentOrders = PurchaseOrder::with('supplier')
            ->latest()
            ->limit(5)
            ->get();

        // Recent GRNs
        $recentGrns = GoodsReceivedNote::with('supplier')
            ->latest()
            ->limit(5)
            ->get();

        return view('purchasing.reports', compact(
            'totalOrders',
            'totalGrns',
            'totalPayments',
            'totalAmount',
            'totalPaid',
            'monthlyOrders',
            'monthlyPayments',
            'topSuppliers',
            'recentOrders',
            'recentGrns'
        ));
    }
}
