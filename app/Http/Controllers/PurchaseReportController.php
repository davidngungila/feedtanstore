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
    public function index(Request $request)
    {
        // Get date filters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build queries with date filters
        $purchaseOrderQuery = PurchaseOrder::query();
        $grnQuery = GoodsReceivedNote::query();
        $supplierPaymentQuery = SupplierPayment::query();

        if ($startDate) {
            $purchaseOrderQuery->whereDate('order_date', '>=', $startDate);
            $grnQuery->whereDate('received_date', '>=', $startDate);
            $supplierPaymentQuery->whereDate('payment_date', '>=', $startDate);
        }

        if ($endDate) {
            $purchaseOrderQuery->whereDate('order_date', '<=', $endDate);
            $grnQuery->whereDate('received_date', '<=', $endDate);
            $supplierPaymentQuery->whereDate('payment_date', '<=', $endDate);
        }

        // Get stats
        $totalOrders = $purchaseOrderQuery->count();
        $totalGrns = $grnQuery->count();
        $totalPayments = $supplierPaymentQuery->count();
        $totalAmount = $purchaseOrderQuery->sum('total');
        $totalPaid = $supplierPaymentQuery->sum('amount');

        // Monthly data for charts - database agnostic
        $allOrders = $purchaseOrderQuery->orderBy('order_date')->get();
        $groupedOrders = $allOrders->groupBy(function($item) {
            return Carbon::parse($item->order_date)->format('Y-m');
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

        $allPayments = $supplierPaymentQuery->orderBy('payment_date')->get();
        $groupedPayments = $allPayments->groupBy(function($item) {
            return Carbon::parse($item->payment_date)->format('Y-m');
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
        $topSuppliers = Supplier::withCount(['purchaseOrders' => function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('order_date', '>=', $startDate);
                if ($endDate) $q->whereDate('order_date', '<=', $endDate);
            }, 'goodsReceivedNotes' => function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('received_date', '>=', $startDate);
                if ($endDate) $q->whereDate('received_date', '<=', $endDate);
            }])
            ->withSum(['purchaseOrders' => function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('order_date', '>=', $startDate);
                if ($endDate) $q->whereDate('order_date', '<=', $endDate);
            }], 'total')
            ->orderBy('purchase_orders_sum_total', 'desc')
            ->limit(10)
            ->get();

        // Recent orders
        $recentOrders = $purchaseOrderQuery->with('supplier')
            ->latest()
            ->limit(5)
            ->get();

        // Recent GRNs
        $recentGrns = $grnQuery->with('supplier')
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
