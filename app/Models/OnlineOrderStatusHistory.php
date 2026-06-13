<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineOrderStatusHistory extends Model
{
    protected $fillable = ['online_order_id', 'status', 'payment_status', 'notes', 'user_id'];

    public function order()
    {
        return $this->belongsTo(OnlineOrder::class, 'online_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
