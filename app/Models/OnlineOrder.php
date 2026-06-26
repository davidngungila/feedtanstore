<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineOrder extends Model {
    protected $fillable = ['order_number', 'customer_id', 'customer_name', 'customer_phone', 'customer_email', 'delivery_address', 'delivery_latitude', 'delivery_longitude', 'status', 'payment_status', 'payment_method', 'payment_transaction_id', 'payment_order_reference', 'clickpesa_status', 'subtotal', 'discount', 'delivery_fee', 'total', 'delivery_rider_id', 'user_id', 'notes', 'is_processed'];
    
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    
    public function items() {
        return $this->hasMany(OnlineOrderItem::class);
    }
    
    public function rider() {
        return $this->belongsTo(DeliveryRider::class, 'delivery_rider_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function statusHistory() {
        return $this->hasMany(OnlineOrderStatusHistory::class)->latest();
    }
}
