<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiderDispatchRequest extends Model
{
    protected $fillable = [
        'online_order_id',
        'dispatch_batch_id',
        'status',
        'accepted_rider_id',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(OnlineOrder::class, 'online_order_id');
    }

    public function dispatchBatch(): BelongsTo
    {
        return $this->belongsTo(RiderDispatchBatch::class, 'dispatch_batch_id');
    }

    public function acceptedRider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class, 'accepted_rider_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(RiderDispatchResponse::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
