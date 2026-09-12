<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockVerificationAdjustment extends Model
{
    protected $fillable = ['session_id','product_id','quantity_before','quantity_after','quantity_change','stock_adjustment_id','stock_movement_id'];
    public function session(): BelongsTo { return $this->belongsTo(StockVerificationSession::class,'session_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
