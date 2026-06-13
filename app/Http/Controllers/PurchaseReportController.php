<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // Monthly data for charts - database agnostic
        $allOrders = PurchaseOrder::orderBy('created_at')->get();
        $groupedOrders = $allOrders->groupBy(function($item) {
            return Carbon::parse($item->created_at)->format('Y-m');
        })->map(function($items, $key) {
            [$year, $month] = explode('-', $key);
            return [
                'year' => (int)$year,
                'month' => (int)$month,
                'count' => $items->count(),
                'total' => $items->sum('total')
            ];
        })->values()->reverse()->take(12);
        $monthlyOrders = $groupedOrders->reverse();

        $allPayments = SupplierPayment::orderBy('created_at')->get();
        $groupedPayments = $allPayments->groupBy(function($item) {
            return Carbon::parse($item->created_at)->format('Y-m');
        })->map(function($items, $key) {
            [$year, $month] = explode('-', $key);
            return [
                'year' => (int)$year,
                'month' => (int)$month,
                'count' => $items->count(),
                'total' => $items->sum('amount')
            ];
        })->values()->reverse()->take(12);
        $monthlyPayments = $groupedPayments->reverse();

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
