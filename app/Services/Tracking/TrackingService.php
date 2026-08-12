<?php

namespace App\Services\Tracking;

use App\Events\DriverLocationUpdated;
use App\Events\TripStatusUpdated;
use App\Models\Customer;
use App\Models\DeliveryRider;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderStatusHistory;
use App\Models\RiderLocation;
use App\Models\StoreSetting;
use App\Models\TrackingSession;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use App\Services\Routing\RoutingService;
use App\Support\Geo;
use App\Support\RouteMath;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrackingService
{
    /** Location is considered stale when the last fix is older than this. */
    public const STALE_AFTER_SECONDS = 60;

    /** Reject a fix that moves the rider faster than this (m/s) between consecutive updates. */
    public const MAX_PLAUSIBLE_SPEED_MS = 45; // ~162 km/h

    public function __construct(
        private readonly RoutingService $routing,
        private readonly NotificationService $notifications,
    ) {}

    public function createSession(OnlineOrder $order, DeliveryRider $rider, ?Customer $customer = null): TrackingSession
    {
        $existing = $this->activeSessionForOrder($order);

        if ($existing) {
            return $existing;
        }

        $store = StoreSetting::first();

        $session = TrackingSession::create([
            'online_order_id' => $order->id,
            'delivery_rider_id' => $rider->id,
            'customer_id' => $customer?->id,
            'pickup_latitude' => $store->store_latitude ?? -3.3869,
            'pickup_longitude' => $store->store_longitude ?? 36.6883,
            'pickup_address' => $store->store_name ?? null,
            'destination_latitude' => $order->delivery_latitude,
            'destination_longitude' => $order->delivery_longitude,
            'destination_address' => $order->delivery_address,
            'status' => TrackingSession::STATUS_ACCEPTED,
        ]);

        broadcast(new TripStatusUpdated($session, TrackingSession::STATUS_ACCEPTED, $rider->user));

        return $session;
    }

    public function activeSessionForOrder(OnlineOrder $order): ?TrackingSession
    {
        return TrackingSession::query()
            ->where('online_order_id', $order->id)
            ->active()
            ->latest('id')
            ->first();
    }

    /**
     * Store a GPS fix sent by the rider app, validate it, update rider presence,
     * and broadcast it to everyone subscribed to the trip channel.
     *
     * @param  array<string, mixed>  $payload
     */
    public function updateLocation(DeliveryRider $rider, array $payload): RiderLocation
    {
        $latitude = (float) $payload['latitude'];
        $longitude = (float) $payload['longitude'];
        $recordedAt = $this->resolveRecordedAt($payload['recorded_at'] ?? null);

        $session = null;
        if (! empty($payload['tracking_session_id'])) {
            $session = TrackingSession::find($payload['tracking_session_id']);
            abort_if(! $session || $session->delivery_rider_id !== $rider->id, 403, 'Tracking session not assigned to you');
            abort_if(! $session->isActive(), 409, 'Tracking session is not active');
        }

        $this->assertPlausible($rider, $latitude, $longitude, $recordedAt);

        $location = RiderLocation::create([
            'delivery_rider_id' => $rider->id,
            'tracking_session_id' => $session?->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'heading' => isset($payload['heading']) ? (float) $payload['heading'] : null,
            'speed' => isset($payload['speed']) ? (float) $payload['speed'] : null,
            'accuracy' => isset($payload['accuracy']) ? (float) $payload['accuracy'] : null,
            'recorded_at' => $recordedAt,
        ]);

        $rider->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        if ($session) {
            broadcast(new DriverLocationUpdated($session, $location));
        }

        return $location;
    }

    /**
     * Transition a tracking session to a new trip status and broadcast the change.
     */
    public function transitionStatus(TrackingSession $session, User $user, string $status): TrackingSession
    {
        $allowed = [
            TrackingSession::STATUS_DRIVER_ARRIVING,
            TrackingSession::STATUS_DRIVER_ARRIVED,
            TrackingSession::STATUS_TRIP_STARTED,
            TrackingSession::STATUS_TRIP_IN_PROGRESS,
            TrackingSession::STATUS_TRIP_COMPLETED,
            TrackingSession::STATUS_CANCELLED,
        ];

        abort_if(! in_array($status, $allowed, true), 422, 'Invalid tracking session status');

        DB::transaction(function () use ($session, $user, $status) {
            $session->markStatus($status);

            $order = $session->order;

            if ($status === TrackingSession::STATUS_TRIP_COMPLETED) {
                $order->update(['status' => 'delivered']);

                OnlineOrderStatusHistory::create([
                    'online_order_id' => $order->id,
                    'status' => 'delivered',
                    'payment_status' => $order->payment_status,
                    'notes' => 'Trip completed via live tracking (rider)',
                    'user_id' => $user->id,
                ]);
            }

            if ($status === TrackingSession::STATUS_CANCELLED) {
                $order->update(['status' => 'cancelled']);

                OnlineOrderStatusHistory::create([
                    'online_order_id' => $order->id,
                    'status' => 'cancelled',
                    'payment_status' => $order->payment_status,
                    'notes' => 'Trip cancelled via live tracking (rider)',
                    'user_id' => $user->id,
                ]);
            }
        });

        broadcast(new TripStatusUpdated($session, $status, $user));

        $this->notifications->sendTripNotification($session, $status);

        return $session->fresh();
    }

    /**
     * Recompute the route from the driver's current position (or pickup) to the
     * destination, cache it on the session, and return the summary.
     *
     * @return array<string, mixed>
     */
    public function recalculateRoute(TrackingSession $session): array
    {
        $latest = $session->locations()->first();
        $origin = $latest
            ? [(float) $latest->latitude, (float) $latest->longitude]
            : [(float) $session->pickup_latitude, (float) $session->pickup_longitude];

        $route = $this->routing->getRoute($origin, [
            (float) $session->destination_latitude,
            (float) $session->destination_longitude,
        ]);

        $routeData = $route->toArray();
        $session->update(['route_data' => $routeData]);

        return $routeData;
    }

    /**
     * Build the payload broadcast with each location update: raw driver fix +
     * cached route + live ETA / remaining distance estimates.
     *
     * @return array<string, mixed>
     */
    public function locationPayload(TrackingSession $session, RiderLocation $location): array
    {
        $route = $session->route_data;
        $destination = [(float) $session->destination_latitude, (float) $session->destination_longitude];

        $distanceRemaining = null;
        $etaSeconds = null;

        if ($route && count($route['polyline'] ?? []) >= 2) {
            $distanceRemaining = RouteMath::remainingDistanceAlongPolyline(
                $route['polyline'],
                [(float) $location->latitude, (float) $location->longitude],
            );

            $avgSpeed = $route['duration_seconds'] > 0
                ? $route['distance_meters'] / $route['duration_seconds']
                : 8.33;

            $speed = $location->speed !== null && $location->speed > 1 ? (float) $location->speed : $avgSpeed;

            $etaSeconds = $speed > 0 ? $distanceRemaining / $speed : null;
        }

        // Straight-line fallback when no route is cached yet
        if ($distanceRemaining === null) {
            $distanceRemaining = Geo::haversine(
                (float) $location->latitude, (float) $location->longitude,
                $destination[0], $destination[1],
            );
        }

        $stale = $location->recorded_at && $location->recorded_at->diffInSeconds(now(), absolute: true) > self::STALE_AFTER_SECONDS;

        return [
            'trip_id' => $session->id,
            'status' => $session->status,
            'driver' => [
                'id' => $session->delivery_rider_id,
                'name' => $session->rider?->name,
                'vehicle_plate' => $session->rider?->vehicle_plate,
                'vehicle_type' => $session->rider?->vehicle_type,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'heading' => $location->heading !== null ? (float) $location->heading : null,
                'speed' => $location->speed !== null ? (float) $location->speed : null,
                'accuracy' => $location->accuracy !== null ? (float) $location->accuracy : null,
            ],
            'distance_remaining' => round((float) $distanceRemaining),
            'eta_seconds' => $etaSeconds !== null ? (int) round($etaSeconds) : null,
            'route' => $route,
            'stale' => $stale,
            'recorded_at' => $location->recorded_at?->toIso8601String(),
        ];
    }

    public function sessionPayload(TrackingSession $session): array
    {
        $latest = $session->locations()->first();

        return [
            'id' => $session->id,
            'status' => $session->status,
            'order_number' => $session->order?->order_number,
            'pickup' => [
                'latitude' => (float) $session->pickup_latitude,
                'longitude' => (float) $session->pickup_longitude,
                'address' => $session->pickup_address,
            ],
            'destination' => [
                'latitude' => (float) $session->destination_latitude,
                'longitude' => (float) $session->destination_longitude,
                'address' => $session->destination_address,
            ],
            'driver' => [
                'id' => $session->delivery_rider_id,
                'name' => $session->rider?->name,
                'phone' => $session->rider?->phone,
                'vehicle_plate' => $session->rider?->vehicle_plate,
                'vehicle_type' => $session->rider?->vehicle_type,
            ],
            'latest_location' => $latest,
            'route' => $session->route_data,
            'started_at' => $session->started_at?->toIso8601String(),
            'completed_at' => $session->completed_at?->toIso8601String(),
            'cancelled_at' => $session->cancelled_at?->toIso8601String(),
        ];
    }

    private function resolveRecordedAt(mixed $value): Carbon
    {
        if ($value) {
            try {
                $recordedAt = Carbon::parse($value);

                // Reject future timestamps (clock skew) and stale fixes older than 5 minutes
                abort_if($recordedAt->isAfter(now()->addSeconds(60)), 422, 'Location timestamp is in the future');
                abort_if($recordedAt->isBefore(now()->subMinutes(5)), 422, 'Location timestamp is too old');

                return $recordedAt;
            } catch (\Throwable) {
                // fall through and use server time
            }
        }

        return now();
    }

    private function assertPlausible(DeliveryRider $rider, float $latitude, float $longitude, Carbon $recordedAt): void
    {
        $previous = RiderLocation::where('delivery_rider_id', $rider->id)->latest('recorded_at')->first();

        if (! $previous) {
            return;
        }

        $elapsed = abs($previous->recorded_at?->diffInSeconds($recordedAt) ?? 0);
        if ($elapsed < 1) {
            $elapsed = 1;
        }

        $distance = Geo::haversine(
            (float) $previous->latitude,
            (float) $previous->longitude,
            $latitude,
            $longitude,
        );

        abort_if($distance / $elapsed > self::MAX_PLAUSIBLE_SPEED_MS, 422, 'Location update rejected: implausible movement speed');
    }
}
