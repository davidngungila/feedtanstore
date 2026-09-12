<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = ['session_id','customer_id','cart_data','cart_total','abandoned_at','is_recovered'];
    protected $casts = ['cart_data'=>'array','abandoned_at'=>'datetime','is_recovered'=>'boolean'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}
