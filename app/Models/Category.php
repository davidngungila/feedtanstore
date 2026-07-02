<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category);
            }
        });
    }

    protected static function generateUniqueSlug($category)
    {
        $baseSlug = Str::slug($category->name . ' moshi');
        $slug = $baseSlug;
        $count = 1;

        $query = static::where('slug', $slug);
        if ($category->exists) {
            $query->where('id', '!=', $category->id);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
            $query = static::where('slug', $slug);
            if ($category->exists) {
                $query->where('id', '!=', $category->id);
            }
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
