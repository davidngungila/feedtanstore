<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDemand extends Model
{
    protected $fillable = [
        'customer_id','customer_name','customer_phone','product_requested','product_requested_normalized',
        'product_id','requested_quantity','request_date','staff_id','branch_id','location_id',
        'note','was_out_of_stock','status','channel','source'
    ];

    protected $casts = [
        'request_date' => 'date',
        'was_out_of_stock' => 'boolean',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function staff(): BelongsTo { return $this->belongsTo(User::class, 'staff_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }

    protected static function booted(): void
    {
        static::saving(function ($model) {
            $model->product_requested_normalized = strtolower(trim($model->product_requested));
        });
    }
}
