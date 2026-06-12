<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineOrder extends Model {
    protected $fillable = ['order_number', 'customer_name', 'customer_phone', 'customer_email', 'delivery_address', 'status', 'payment_status', 'payment_method', 'subtotal', 'delivery_fee', 'total', 'delivery_rider_id', 'user_id', 'notes'];
    
    public function items() {
        return $this->hasMany(OnlineOrderItem::class);
    }
    
    public function rider() {
        return $this->belongsTo(DeliveryRider::class, 'delivery_rider_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}
