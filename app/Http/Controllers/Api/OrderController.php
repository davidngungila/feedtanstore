<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        $orders = OnlineOrder::where('delivery_rider_id', $rider->id)
            ->with(['items.product', 'customer'])
            ->latest()
            ->get();
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = OnlineOrder::with(['items.product', 'customer', 'rider', 'statusHistory'])->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,ready,out_for_delivery,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $order = OnlineOrder::findOrFail($id);
            $oldStatus = $order->status;
            $order->update(['status' => $request->status]);

            OnlineOrderStatusHistory::create([
                'online_order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'notes' => $request->notes,
                'changed_by' => $request->user()->id,
            ]);

            DB::commit();
            return response()->json(['message' => 'Order status updated', 'order' => $order]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update order status'], 500);
        }
    }

    public function available(Request $request)
    {
        $orders = OnlineOrder::whereNull('delivery_rider_id')
            ->where('status', 'confirmed')
            ->with(['items.product', 'customer'])
            ->latest()
            ->get();
        return response()->json($orders);
    }

    public function accept(Request $request, $id)
    {
        $rider = $request->user()->deliveryRider;
        $order = OnlineOrder::findOrFail($id);

        if ($order->delivery_rider_id && $order->delivery_rider_id != $rider->id) {
            return response()->json(['message' => 'Order already assigned to another rider'], 400);
        }

        if ($order->rider_acceptance_status === 'accepted') {
            return response()->json(['message' => 'Order already accepted'], 400);
        }

        $order->update([
            'delivery_rider_id' => $rider->id, 
            'status' => 'out_for_delivery',
            'rider_acceptance_status' => 'accepted',
            'rider_accepted_at' => now()
        ]);
        
        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'old_status' => $order->status,
            'new_status' => 'out_for_delivery',
            'notes' => 'Order accepted by rider via API',
            'changed_by' => $request->user()->id,
        ]);
        
        return response()->json(['message' => 'Order accepted', 'order' => $order]);
    }
    
    public function reject(Request $request, $id)
    {
        $rider = $request->user()->deliveryRider;
        $order = OnlineOrder::findOrFail($id);

        if (!$order->delivery_rider_id || $order->delivery_rider_id != $rider->id) {
            return response()->json(['message' => 'Order not assigned to you'], 400);
        }

        if ($order->rider_acceptance_status === 'accepted') {
            return response()->json(['message' => 'Cannot reject accepted order'], 400);
        }

        $order->update([
            'rider_acceptance_status' => 'rejected',
            'delivery_rider_id' => null,
            'status' => 'confirmed'
        ]);
        
        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'old_status' => $order->status,
            'new_status' => 'confirmed',
            'notes' => 'Order rejected by rider via API',
            'changed_by' => $request->user()->id,
        ]);
        
        return response()->json(['message' => 'Order rejected', 'order' => $order]);
    }
}