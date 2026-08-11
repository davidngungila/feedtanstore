<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackingSession extends Model
{
    public const STATUS_REQUESTED = 'requested';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DRIVER_ARRIVING = 'driver_arriving';
    public const STATUS_DRIVER_ARRIVED = 'driver_arrived';
    public const STATUS_TRIP_STARTED = 'trip_started';
    public const STATUS_TRIP_IN_PROGRESS = 'trip_in_progress';
    public const STATUS_TRIP_COMPLETED = 'trip_completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const ACTIVE_STATUSES = [
        self::STATUS_ACCEPTED,
        self::STATUS_DRIVER_ARRIVING,
        self::STATUS_DRIVER_ARRIVED,
        self::STATUS_TRIP_STARTED,
        self::STATUS_TRIP_IN_PROGRESS,
    ];

    protected $fillable = [
        'online_order_id',
        'delivery_rider_id',
        'customer_id',
        'pickup_latitude',
        'pickup_longitude',
        'pickup_address',
        'destination_latitude',
        'destination_longitude',
        'destination_address',
        'status',
        'started_at',
        'driver_arriving_at',
        'driver_arrived_at',
        'completed_at',
        'cancelled_at',
        'route_data',
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'destination_latitude' => 'decimal:7',
        'destination_longitude' => 'decimal:7',
        'started_at' => 'datetime',
        'driver_arriving_at' => 'datetime',
        'driver_arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'route_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(OnlineOrder::class, 'online_order_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class, 'delivery_rider_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(RiderLocation::class, 'tracking_session_id')->latest('recorded_at');
    }

    public function latestLocation(): HasMany
    {
        return $this->hasMany(RiderLocation::class, 'tracking_session_id')->latest('recorded_at')->limit(1);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    public function markStatus(string $status): void
    {
        $timestamps = [
            self::STATUS_DRIVER_ARRIVING => 'driver_arriving_at',
            self::STATUS_DRIVER_ARRIVED => 'driver_arrived_at',
            self::STATUS_TRIP_STARTED => 'started_at',
            self::STATUS_TRIP_COMPLETED => 'completed_at',
            self::STATUS_CANCELLED => 'cancelled_at',
        ];

        $this->status = $status;
        if (isset($timestamps[$status]) && $this->{$timestamps[$status]} === null) {
            $this->{$timestamps[$status]} = now();
        }
        $this->save();
    }
}
