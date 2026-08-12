<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderStatusHistory;
use App\Services\Notifications\NotificationService;
use App\Services\Tracking\TrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private readonly TrackingService $tracking,
        private readonly NotificationService $notifications,
    ) {}

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
            'status' => 'required|in:out_for_delivery,delivered',
            'delivery_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $order = OnlineOrder::findOrFail($id);
            $rider = $request->user()->deliveryRider;

            if (! $order->delivery_rider_id || $order->delivery_rider_id != $rider->id) {
                return response()->json(['message' => 'Order not assigned to you'], 403);
            }

            // Validate the 4-digit delivery code when marking as delivered
            if ($request->status === 'delivered' && trim((string) $request->delivery_code) !== $order->delivery_code) {
                return response()->json(['message' => 'Invalid delivery code. Please enter the correct 4-digit code.'], 422);
            }

            $oldStatus = $order->status;
            $order->update(['status' => $request->status]);

            $statusChangeNote = $request->notes;
            if (! $statusChangeNote && $oldStatus !== $order->status) {
                $statusChangeNote = "Status changed from {$oldStatus} to {$order->status} by rider via API";
            }

            OnlineOrderStatusHistory::create([
                'online_order_id' => $order->id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'notes' => $statusChangeNote,
                'user_id' => $request->user()->id,
            ]);

            if ($order->status === 'delivered') {
                $this->notifications->sendOrderNotification($order, 'delivered');
            } elseif ($order->status === 'out_for_delivery') {
                $this->notifications->sendOrderNotification($order, 'dispatched');
            }

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
            ->where('packaging_status', 'completed')
            ->where('reconciliation_status', 'completed')
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

        if (! $order->delivery_rider_id) {
            // Rider is self-assigning an available order; enforce packaging & reconciliation
            if ($order->packaging_status !== 'completed') {
                return response()->json(['message' => 'Order packaging is not completed yet'], 400);
            }
            if ($order->reconciliation_status !== 'completed') {
                return response()->json(['message' => 'Order reconciliation is not completed yet'], 400);
            }
        }

        $oldStatus = $order->status;

        $order->update([
            'delivery_rider_id' => $rider->id,
            'status' => 'out_for_delivery',
            'rider_acceptance_status' => 'accepted',
            'rider_accepted_at' => now(),
        ]);

        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => 'out_for_delivery',
            'payment_status' => $order->payment_status,
            'notes' => 'Order accepted by rider via API (status changed from '.$oldStatus.' to out_for_delivery)',
            'user_id' => $request->user()->id,
        ]);

        // Start a live tracking session for the trip
        $this->tracking->createSession($order, $rider, $order->customer);

        $this->notifications->sendOrderNotification($order, 'accepted');

        $session = $this->tracking->activeSessionForOrder($order);
        if ($session) {
            $this->notifications->sendTripNotification($session, 'accepted');
        }

        return response()->json([
            'message' => 'Order accepted',
            'order' => $order,
            'tracking_session_id' => $this->tracking->activeSessionForOrder($order)?->id,
        ]);
    }

    public function reject(Request $request, $id)
    {
        $rider = $request->user()->deliveryRider;
        $order = OnlineOrder::findOrFail($id);

        if (! $order->delivery_rider_id || $order->delivery_rider_id != $rider->id) {
            return response()->json(['message' => 'Order not assigned to you'], 400);
        }

        if ($order->rider_acceptance_status === 'accepted') {
            return response()->json(['message' => 'Cannot reject accepted order'], 400);
        }

        $oldStatus = $order->status;

        $order->update([
            'rider_acceptance_status' => 'rejected',
            'delivery_rider_id' => null,
            'status' => 'confirmed',
        ]);

        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => 'confirmed',
            'payment_status' => $order->payment_status,
            'notes' => 'Order rejected by rider via API (status changed from '.$oldStatus.' to confirmed), unassigned',
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Order rejected', 'order' => $order]);
    }
}
