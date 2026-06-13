<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = OnlineOrder::with(['items', 'rider', 'user'])->latest();
        
        if ($request->has('order_number') && $request->order_number) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }
        
        $orders = $query->get();
        return view('online.tracking', compact('orders'));
    }

    public function show($orderNumber)
    {
        $order = OnlineOrder::where('order_number', $orderNumber)->with(['items', 'rider'])->firstOrFail();
        return view('online.tracking-show', compact('order'));
    }
}
