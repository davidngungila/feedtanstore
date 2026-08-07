<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\Customer;
use App\Models\DeliveryRider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingOfficerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get online order statistics
        $totalOrders = OnlineOrder::count();
        $pendingOrders = OnlineOrder::where('status', 'pending')->count();
        $confirmedOrders = OnlineOrder::where('status', 'confirmed')->count();
        $outForDeliveryOrders = OnlineOrder::where('status', 'out_for_delivery')->count();
        $deliveredOrders = OnlineOrder::where('status', 'delivered')->count();
        $cancelledOrders = OnlineOrder::where('status', 'cancelled')->count();
        
        // Total revenue from online orders
        $totalRevenue = OnlineOrder::where('status', '!=', 'cancelled')->sum('total');
        
        // Recent orders
        $recentOrders = OnlineOrder::with('rider')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Orders by status
        $ordersByStatus = OnlineOrder::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Available riders
        $availableRiders = DeliveryRider::where('is_active', true)->count();
        $totalRiders = DeliveryRider::count();
        
        return view('marketing-officer.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'confirmedOrders',
            'outForDeliveryOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalRevenue',
            'recentOrders',
            'ordersByStatus',
            'availableRiders',
            'totalRiders'
        ));
    }

    public function orders()
    {
        $orders = OnlineOrder::with('rider', 'customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('marketing-officer.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $order = OnlineOrder::with('rider', 'customer', 'items')->findOrFail($id);
        return view('marketing-officer.order-details', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = OnlineOrder::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function assignRider(Request $request, $id)
    {
        $request->validate([
            'rider_id' => 'required|exists:delivery_riders,id',
        ]);

        $order = OnlineOrder::findOrFail($id);
        $order->delivery_rider_id = $request->rider_id;
        $order->rider_acceptance_status = 'pending';
        $order->status = 'confirmed';
        $order->save();
        
        return redirect()->back()->with('success', 'Rider assigned successfully');
    }

    public function requestPackaging(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $order = OnlineOrder::findOrFail($id);
        
        // Create stock request for packaging
        $stockRequest = \App\Models\StockRequest::create([
            'user_id' => Auth::id(),
            'online_order_id' => $order->id,
            'request_type' => 'online_order',
            'status' => 'pending',
            'notes' => $request->notes ?? 'Packaging request for order',
        ]);

        // Add order items to stock request
        foreach ($order->items as $item) {
            \App\Models\StockRequestItem::create([
                'stock_request_id' => $stockRequest->id,
                'product_id' => $item->product_id,
                'quantity_requested' => $item->quantity,
                'quantity_approved' => 0,
                'notes' => 'Packaging for order ' . $order->order_number,
            ]);
        }

        return redirect()->back()->with('success', 'Packaging request submitted successfully');
    }

    public function trackDelivery($id)
    {
        $order = OnlineOrder::with(['rider', 'rider.latestLocation', 'statusHistory'])->findOrFail($id);
        return view('marketing-officer.track-delivery', compact('order'));
    }

    public function customers()
    {
        $customers = Customer::orderBy('name')->paginate(20);
        return view('marketing-officer.customers', compact('customers'));
    }

    public function riders()
    {
        $riders = DeliveryRider::with('user')->orderBy('name')->paginate(20);
        return view('marketing-officer.riders', compact('riders'));
    }
}
