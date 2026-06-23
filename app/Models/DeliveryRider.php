<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeliveryRider extends Model
{
    protected $fillable = ['name', 'phone', 'vehicle_type', 'vehicle_plate', 'is_active', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function onlineOrders(): HasMany
    {
        return $this->hasMany(OnlineOrder::class, 'delivery_rider_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(RiderLocation::class)->latest();
    }

    public function latestLocation(): HasOne
    {
        return $this->hasOne(RiderLocation::class)->latestOfMany();
    }
}
