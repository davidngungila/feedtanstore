<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderDispatchResponse extends Model
{
    protected $fillable = [
        'rider_dispatch_request_id',
        'delivery_rider_id',
        'response',
    ];

    public function dispatchRequest(): BelongsTo
    {
        return $this->belongsTo(RiderDispatchRequest::class, 'rider_dispatch_request_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class, 'delivery_rider_id');
    }
}
