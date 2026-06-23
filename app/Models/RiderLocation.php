<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderLocation extends Model
{
    protected $fillable = ['delivery_rider_id', 'latitude', 'longitude'];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class);
    }
}
