<?php

namespace App\Http\Controllers\Api;

use App\Events\TripCancelled;
use App\Events\TripCompleted;
use App\Models\OnlineOrder;
use App\Models\TrackingSession;
use App\Services\Tracking\TrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrackingSessionController extends Controller
{
    public function __construct(
        private readonly TrackingService $tracking,
    ) {
    }

    /**
     * Rider app: receive a GPS fix (driver -> backend -> subscribers).
     */
    public function storeLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'heading' => 'nullable|numeric|between:0,360',
            'speed' => 'nullable|numeric|min:0|max:200',
            'accuracy' => 'nullable|numeric|min:0|max:5000',
            'recorded_at' => 'nullable|date',
            'tracking_session_id' => 'nullable|integer',
        ]);

        $rider = $request->user()->deliveryRider;

        abort_if(! $rider, 403, 'Authenticated user is not a rider');

        $this->tracking->updateLocation($rider, $request->all());

        return response()->json([
            'message' => 'Location updated',
            'is_online' => true,
            'last_seen_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Rider app: set online/offline presence.
     */
    public function presence(Request $request)
    {
        $request->validate([
            'online' => 'required|boolean',
        ]);

        $rider = $request->user()->deliveryRider;

        abort_if(! $rider, 403, 'Authenticated user is not a rider');

        $rider->update([
            'is_online' => $request->boolean('online'),
            'last_seen_at' => now(),
        ]);

        return response()->json([
            'is_online' => $rider->is_online,
            'last_seen_at' => $rider->last_seen_at,
        ]);
    }

    /**
     * Rider app: list this rider's active tracking sessions.
     */
    public function index(Request $request)
    {
        $rider = $request->user()->deliveryRider;

        abort_if(! $rider, 403, 'Authenticated user is not a rider');

        $sessions = TrackingSession::with('order')
            ->where('delivery_rider_id', $rider->id)
            ->active()
            ->latest()
            ->get()
            ->map(fn (TrackingSession $session) => $this->tracking->sessionPayload($session));

        return response()->json($sessions);
    }

    /**
     * Trip session details for an authorized participant (rider or staff).
     */
    public function show(Request $request, $id)
    {
        $session = TrackingSession::findOrFail($id);

        $this->authorizeParticipant($request->user(), $session);

        return response()->json($this->tracking->sessionPayload($session));
    }

    /**
     * Rider app: transition the trip status and broadcast it.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $session = TrackingSession::findOrFail($id);

        $this->authorizeRider($request->user(), $session);

        $updated = $this->tracking->transitionStatus($session, $request->user(), $request->status);

        if ($updated->status === TrackingSession::STATUS_TRIP_COMPLETED) {
            broadcast(new TripCompleted($updated));
        } elseif ($updated->status === TrackingSession::STATUS_CANCELLED) {
            broadcast(new TripCancelled($updated));
        }

        return response()->json($this->tracking->sessionPayload($updated));
    }

    /**
     * Compute (and cache) the driving route for a trip, optionally recalculating
     * from the driver's current position. Clients call this on off-route detection.
     */
    public function route(Request $request, $id)
    {
        $session = TrackingSession::findOrFail($id);

        $this->authorizeParticipant($request->user(), $session);

        return response()->json([
            'route' => $this->tracking->recalculateRoute($session),
            'recalculated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Public read-only lookup: active tracking session for an order (used by the
     * shop tracking page). Only exposes data for this specific order.
     */
    public function byOrder($orderNumber)
    {
        $order = OnlineOrder::where('order_number', $orderNumber)
            ->orWhere('tracking_token', $orderNumber)
            ->with('rider')
            ->firstOrFail();

        $session = $this->tracking->activeSessionForOrder($order);

        if (! $session) {
            return response()->json(['session' => null, 'order_status' => $order->status]);
        }

        $payload = $this->tracking->sessionPayload($session);
        $latest = $session->locations()->first();

        if ($latest) {
            $payload['live_location'] = $this->tracking->locationPayload($session, $latest);
        }

        return response()->json([
            'session' => $payload,
            'order_status' => $order->status,
        ]);
    }

    private function authorizeParticipant(\Illuminate\Contracts\Auth\Authenticatable $user, TrackingSession $session): void
    {
        $this->authorizeRider($user, $session);
    }

    private function authorizeRider(\Illuminate\Contracts\Auth\Authenticatable $user, TrackingSession $session): void
    {
        $isStaff = in_array($user->role, ['admin', 'manager', 'marketing_officer'], true);
        $isRider = $session->delivery_rider_id === $user->deliveryRider?->id;

        abort_if(! $isStaff && ! $isRider, 403, 'You are not a participant of this trip');
    }
}
