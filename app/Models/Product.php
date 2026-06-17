<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category_id',
        'brand_id',
        'unit_id',
        'description',
        'cost_price',
        'selling_price',
        'quantity',
        'reorder_level',
        'expiry_date',
        'batch_number',
        'image',
        'is_active',
        'is_available_online',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function grnItems(): HasMany
    {
        return $this->hasMany(GrnItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
