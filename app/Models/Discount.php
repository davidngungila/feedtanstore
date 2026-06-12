<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model {
    protected $fillable = ['name', 'type', 'value', 'min_amount', 'max_amount', 'start_date', 'end_date', 'is_active', 'requires_approval'];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean'
    ];

    public function sales() {
        return $this->hasMany(Sale::class);
    }
}
