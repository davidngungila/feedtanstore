<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderDispatchBatchResponse extends Model
{
    protected $fillable = [
        'rider_dispatch_batch_id',
        'delivery_rider_id',
        'response',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(RiderDispatchBatch::class, 'rider_dispatch_batch_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class, 'delivery_rider_id');
    }
}
