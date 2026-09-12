<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldSalesOrderItem extends Model
{
    protected $fillable = ['field_sales_order_id','product_id','quantity','unit_price','cost_price','discount','total'];
    public function order(): BelongsTo { return $this->belongsTo(FieldSalesOrder::class,'field_sales_order_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
