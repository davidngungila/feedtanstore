<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldSalesOrder extends Model
{
    protected $fillable = [
        'order_number','local_transaction_id','customer_id','customer_name','customer_phone','sales_rep_id',
        'driver_code','driver_name','delivery_rider_id','reference_code','sales_date',
        'branch_id','location_id','subtotal','discount','tax','total','payment_status','status','payment_method',
        'paid_amount','outstanding_amount','cost_of_goods_sold','gross_profit','sync_status','synced_at','offline_created_at','device_info','notes'
    ];

    protected $casts = [
        'synced_at'=>'datetime',
        'offline_created_at'=>'datetime',
        'sales_date'=>'date',
    ];

    public function deliveryRider(): BelongsTo { return $this->belongsTo(\App\Models\DeliveryRider::class, 'delivery_rider_id'); }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function salesRep(): BelongsTo { return $this->belongsTo(User::class,'sales_rep_id'); }
    public function items(): HasMany { return $this->hasMany(FieldSalesOrderItem::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }

    public static function generateNumber(): string
    {
        return 'FS-'.date('YmdHis').'-'.strtoupper(\Illuminate\Support\Str::random(4));
    }
}
