<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class DeliveryRider extends Model
{
    protected $fillable = [
        'name', 'phone', 'vehicle_type', 'vehicle_plate', 'is_active', 'is_online', 'last_seen_at', 'user_id',
        'date_of_birth', 'gender', 'address', 'profile_image', 'nid_number',
        'driving_license_number', 'license_expiry_date',
        'vehicle_model', 'vehicle_color', 'vehicle_year',
        'insurance_number', 'insurance_expiry_date',
        'bank_name', 'bank_account_number', 'bank_account_name', 'bank_branch',
        'mobile_money_number', 'mobile_money_provider',
        'total_deliveries', 'total_earnings', 'rating', 'total_reviews'
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    protected $appends = ['profile_image_url'];

    public function getProfileImageUrlAttribute(): ?string
    {
        return $this->profile_image
            ? Storage::disk('public')->url($this->profile_image)
            : null;
    }

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

    public function reviews(): HasMany
    {
        return $this->hasMany(RiderReview::class)->latest();
    }
}
