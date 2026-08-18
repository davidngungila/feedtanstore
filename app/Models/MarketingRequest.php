<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingRequest extends Model
{
    protected $fillable = [
        'request_number',
        'requested_by',
        'title',
        'description',
        'status',
        'processed_by',
        'storekeeper_notes',
        'accepted_at',
        'processed_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MarketingRequestItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->request_number)) {
                $request->request_number = 'MR-' . date('YmdHis');
            }
        });
    }
}
