<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderReview extends Model
{
    protected $fillable = [
        'delivery_rider_id', 'online_order_id', 'customer_name', 'customer_email', 'rating', 'comment'
    ];

    public function deliveryRider(): BelongsTo
    {
        return $this->belongsTo(DeliveryRider::class);
    }

    public function onlineOrder(): BelongsTo
    {
        return $this->belongsTo(OnlineOrder::class);
    }
}
