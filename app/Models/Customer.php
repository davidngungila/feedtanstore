<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    protected $fillable = ['name', 'email', 'phone', 'address', 'credit_limit', 'balance', 'customer_group_id'];

    public function sales() {
        return $this->hasMany(Sale::class);
    }

    public function payments() {
        return $this->hasMany(CustomerPayment::class);
    }

    public function group() {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function loyaltyPoints() {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function getTotalLoyaltyPointsAttribute() {
        return $this->loyaltyPoints()->where('type', 'earned')->sum('points') - $this->loyaltyPoints()->where('type', 'redeemed')->sum('points');
    }
}
