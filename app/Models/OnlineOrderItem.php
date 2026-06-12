<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineOrderItem extends Model {
    protected $fillable = ['online_order_id', 'product_id', 'quantity', 'price', 'total'];
    
    public function order() {
        return $this->belongsTo(OnlineOrder::class, 'online_order_id');
    }
    
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
