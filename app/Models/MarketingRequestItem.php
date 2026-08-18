<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingRequestItem extends Model
{
    protected $fillable = [
        'marketing_request_id',
        'product_id',
        'quantity_requested',
        'quantity_provided',
        'unit_price',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function marketingRequest(): BelongsTo
    {
        return $this->belongsTo(MarketingRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
