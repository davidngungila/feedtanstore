<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class RiderDispatchBatch extends Model
{
    protected $fillable = [
        'batch_number',
        'status',
        'created_by',
        'target_rider_id',
        'accepted_rider_id',
        'accepted_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            if (empty($batch->batch_number)) {
                $batch->batch_number = static::generateBatchNumber();
            }
        });
    }

    public static function generateBatchNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "BTCH-{$date}-";

        $lastBatch = static::where('batch_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastBatch) {
            $lastSequence = (int) substr($lastBatch->batch_number, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(RiderDispatchRequest::class, 'dispatch_batch_id');
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            OnlineOrder::class,
            RiderDispatchRequest::class,
            'dispatch_batch_id',
            'id',
            'id',
            'online_order_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetRider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class, 'target_rider_id');
    }

    public function acceptedRider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class, 'accepted_rider_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(RiderDispatchBatchResponse::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNotRespondedBy($query, DeliveryRider $rider)
    {
        return $query->whereDoesntHave('responses', function ($q) use ($rider) {
            $q->where('delivery_rider_id', $rider->id);
        });
    }
}
