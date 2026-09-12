<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRating extends Model
{
    protected $fillable = [
        'sale_id','online_order_id','transaction_reference','customer_id','customer_name','customer_phone',
        'branch_id','location_id','staff_id','rating','staff_service','waiting_time','product_availability',
        'price_rating','cleanliness','overall_experience','comment'
    ];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function onlineOrder(): BelongsTo { return $this->belongsTo(OnlineOrder::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function staff(): BelongsTo { return $this->belongsTo(User::class, 'staff_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
