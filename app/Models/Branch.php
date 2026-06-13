<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'manager_name',
        'location_id',
        'is_active'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
