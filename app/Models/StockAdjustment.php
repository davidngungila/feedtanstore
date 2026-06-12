<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    protected $fillable = [
        'reference_number',
        'product_id',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'type',
        'reason',
        'adjustment_date',
        'notes'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
