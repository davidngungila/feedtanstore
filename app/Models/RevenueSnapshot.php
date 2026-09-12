<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date','branch_id','sales_channel','gross_sales','discounts','tax','net_sales',
        'cost_of_goods_sold','gross_profit','payment_amount','outstanding_amount','refunds','final_revenue','orders_count'
    ];
    protected $casts = ['snapshot_date'=>'date'];
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
