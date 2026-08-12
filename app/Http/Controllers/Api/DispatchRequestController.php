<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineOrderStatusHistory;
use App\Models\RiderDispatchRequest;
use App\Models\RiderDispatchResponse;
use App\Services\Notifications\NotificationService;
use App\Services\Tracking\TrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispatchRequestController extends Controller
{
    public function __construct(
        private readonly TrackingService $tracking,
        private readonly NotificationService $notifications,
    ) {}

    /**
     * List pending dispatch requests available to the authenticated rider.
     * A request disappears once accepted by another rider or once this rider has responded.
     */
    public function index(Request $request)
    {
        $rider = $request->user()->deliveryRider;

        // Auto-expire overdue requests
        RiderDispatchRequest::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'cancelled']);

        $requests = RiderDispatchRequest::with(['order.items.product', 'order.customer'])
            ->pending()
            ->whereHas('order', function ($q) {
                $q->whereNull('delivery_rider_id');
            })
            ->whereDoesntHave('responses', function ($q) use ($rider) {
                $q->where('delivery_rider_id', $rider->id);
            })
            ->latest()
            ->get();

        return response()->json($requests);
    }

    /**
     * Accept a dispatch request. The first rider to accept gets the order
     * assigned immediately; the request then disappears for every other rider.
     */
    public function accept(Request $request, $id)
    {
        $rider = $request->user()->deliveryRider;

        DB::beginTransaction();
        try {
            $dispatch = RiderDispatchRequest::with('order')
                ->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($dispatch->status !== 'pending') {
                DB::rollBack();

                return response()->json(['message' => 'This dispatch request has already been handled'], 409);
            }

            $order = $dispatch->order;

            if ($order->delivery_rider_id) {
                DB::rollBack();

                return response()->json(['message' => 'Order already assigned to another rider'], 409);
            }

            if ($order->packaging_status !== 'completed' || $order->reconciliation_status !== 'completed') {
                DB::rollBack();

                return response()->json(['message' => 'Order is not ready for dispatch'], 409);
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
                'notes' => 'Dispatch request accepted by rider via API (status changed from '.$oldStatus.' to out_for_delivery)',
                'user_id' => $request->user()->id,
            ]);

            $dispatch->update([
                'status' => 'accepted',
                'accepted_rider_id' => $rider->id,
                'accepted_at' => now(),
            ]);

            RiderDispatchResponse::updateOrCreate(
                [
                    'rider_dispatch_request_id' => $dispatch->id,
                    'delivery_rider_id' => $rider->id,
                ],
                ['response' => 'accepted']
            );

            DB::commit();

            // Start a live tracking session for the trip (broadcasts trip.status.updated)
            $this->tracking->createSession($order, $rider, $order->customer);

            $this->notifications->sendOrderNotification($order, 'accepted');

            $session = $this->tracking->activeSessionForOrder($order);
            if ($session) {
                $this->notifications->sendTripNotification($session, 'accepted');
            }

            return response()->json([
                'message' => 'Dispatch request accepted. Order assigned to you.',
                'order' => $order,
                'tracking_session_id' => $this->tracking->activeSessionForOrder($order)?->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Decline a dispatch request for this rider. It stays visible to other riders.
     */
    public function decline(Request $request, $id)
    {
        $rider = $request->user()->deliveryRider;

        $dispatch = RiderDispatchRequest::findOrFail($id);

        if ($dispatch->status !== 'pending') {
            return response()->json(['message' => 'This dispatch request has already been handled'], 409);
        }

        RiderDispatchResponse::updateOrCreate(
            [
                'rider_dispatch_request_id' => $dispatch->id,
                'delivery_rider_id' => $rider->id,
            ],
            ['response' => 'declined']
        );

        return response()->json(['message' => 'Dispatch request declined']);
    }
}
