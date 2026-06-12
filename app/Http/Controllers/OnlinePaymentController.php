<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use Illuminate\Http\Request;

class OnlinePaymentController extends Controller
{
    public function index()
    {
        $orders = OnlineOrder::with(['items', 'rider'])->latest()->get();
        $paidOrdersCount = OnlineOrder::where('payment_status', 'Paid')->count();
        $pendingOrdersCount = OnlineOrder::where('payment_status', 'Pending')->count();
        $totalPaidAmount = OnlineOrder::where('payment_status', 'Paid')->sum('total');
        
        return view('online.payments', compact('orders', 'paidOrdersCount', 'pendingOrdersCount', 'totalPaidAmount'));
    }
}