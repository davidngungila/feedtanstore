<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'barcode',
        'category_id',
        'brand_id',
        'unit_id',
        'description',
        'specifications',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product);
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product);
            }
            // If name changed and we had a slug, update it
            if ($product->isDirty('name') && $product->slug) {
                $product->slug = static::generateUniqueSlug($product);
            }
        });

        static::saving(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product);
            }
        });
    }

    protected static function generateUniqueSlug($product)
    {
        $baseSlug = Str::slug($product->name . ' moshi');
        $slug = $baseSlug;
        $count = 1;

        $query = static::where('slug', $slug);
        if ($product->exists) {
            $query->where('id', '!=', $product->id);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
            $query = static::where('slug', $slug);
            if ($product->exists) {
                $query->where('id', '!=', $product->id);
            }
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        // Fall back to id if slug is empty
        return $this->slug ? 'slug' : 'id';
    }

    // Override getRouteKey to handle null slug
    public function getRouteKey()
    {
        return $this->slug ?: $this->getKey();
    }

    protected $casts = [
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'is_available_online' => 'boolean',
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

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function onlineOrderItems(): HasMany
    {
        return $this->hasMany(OnlineOrderItem::class);
    }
}
