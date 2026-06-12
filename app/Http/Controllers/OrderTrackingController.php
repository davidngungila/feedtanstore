<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        $orders = OnlineOrder::with(['items', 'rider', 'user'])->latest()->get();
        return view('online.tracking', compact('orders'));
    }

    public function show($orderNumber)
    {
        $order = OnlineOrder::where('order_number', $orderNumber)->with(['items', 'rider'])->firstOrFail();
        return view('online.tracking-show', compact('order'));
    }
}
