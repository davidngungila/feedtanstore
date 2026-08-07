<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockRequest extends Model
{
    protected $fillable = [
        'request_number',
        'user_id',
        'online_order_id',
        'request_type',
        'status',
        'notes',
        'requested_at',
        'approved_at',
        'approved_by',
        'completed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function onlineOrder(): BelongsTo
    {
        return $this->belongsTo(OnlineOrder::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockRequestItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stockRequest) {
            if (empty($stockRequest->request_number)) {
                $stockRequest->request_number = 'STR-' . date('YmdHis');
            }
            if (empty($stockRequest->requested_at)) {
                $stockRequest->requested_at = now();
            }
        });
    }
}
