<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use App\Models\Product;
use App\Models\DeliveryRider;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineOrderController extends Controller
{
    public function index()
    {
        $orders = OnlineOrder::with(['items', 'rider', 'user'])->latest()->get();
        return view('online.orders', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_available_online', true)->where('quantity', '>', 0)->get();
        $riders = DeliveryRider::where('is_active', true)->get();
        return view('online.orders-create', compact('products', 'riders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'delivery_address' => 'required|string',
            'payment_method' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_rider_id' => 'nullable|exists:delivery_riders,id',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $subtotal += $product->selling_price * $item['quantity'];
        }

        $deliveryFee = $request->delivery_fee ?? 0;
        $total = $subtotal + $deliveryFee;

        $order = OnlineOrder::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'delivery_address' => $request->delivery_address,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'delivery_rider_id' => $request->delivery_rider_id,
            'user_id' => Auth::id(),
            'notes' => $request->notes
        ]);

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            OnlineOrderItem::create([
                'online_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->selling_price,
                'total' => $product->selling_price * $item['quantity']
            ]);
        }

        return redirect()->route('online.orders')->with('success', 'Online Order created successfully!');
    }

    public function show(OnlineOrder $order)
    {
        $order->load(['items.product', 'rider', 'user']);
        return view('online.orders-show', compact('order'));
    }

    public function updateStatus(Request $request, OnlineOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed'
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status ?? $order->payment_status
        ]);

        // If order is delivered and paid, create accounting entries and reduce stock
        if ($request->status === 'delivered' && $request->payment_status === 'paid') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                $product->update(['quantity' => $product->quantity - $item->quantity]);
            }

            AccountingEntry::create([
                'reference_number' => $order->order_number,
                'reference_type' => OnlineOrder::class,
                'account' => 'Cash',
                'type' => 'debit',
                'amount' => $order->total,
                'description' => 'Online Order Payment'
            ]);

            AccountingEntry::create([
                'reference_number' => $order->order_number,
                'reference_type' => OnlineOrder::class,
                'account' => 'Sales',
                'type' => 'credit',
                'amount' => $order->total,
                'description' => 'Online Order'
            ]);
        }

        return back()->with('success', 'Order status updated successfully!');
    }

    public function assignRider(Request $request, OnlineOrder $order)
    {
        $request->validate([
            'delivery_rider_id' => 'required|exists:delivery_riders,id'
        ]);

        $order->update(['delivery_rider_id' => $request->delivery_rider_id, 'status' => 'out_for_delivery']);

        return back()->with('success', 'Rider assigned successfully!');
    }
}
