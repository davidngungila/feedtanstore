<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\StoreSetting;
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

    public function show($identifier)
    {
        // Try to find by order_number first
        $order = OnlineOrder::where('order_number', $identifier)->orWhere('tracking_token', $identifier)->with(['items', 'rider'])->first();
        
        if (!$order) {
            // Try to find by short customer reference (look for orders where short_customer_reference is #$identifier)
            $orders = OnlineOrder::with(['items', 'rider'])->get();
            foreach ($orders as $o) {
                if (ltrim($o->short_customer_reference, '#') === $identifier) {
                    $order = $o;
                    break;
                }
            }
        }
        
        if (!$order) {
            abort(404, 'Order not found');
        }
        
        $settings = StoreSetting::firstOrCreate();
        return view('online.tracking-show', compact('order', 'settings'));
    }
}
