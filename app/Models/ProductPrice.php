<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'price',
        'label',
        'is_active',
        'activated_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Activate this price and deactivate all other prices of the same product.
     * Keeps products.selling_price in sync so sales/POS always use the active price.
     */
    public function activate(): void
    {
        \DB::transaction(function () {
            static::where('product_id', $this->product_id)
                ->where('id', '!=', $this->id)
                ->update(['is_active' => false]);

            $this->update([
                'is_active' => true,
                'activated_at' => now(),
            ]);

            Product::where('id', $this->product_id)
                ->update(['selling_price' => $this->price]);
        });
    }
}
