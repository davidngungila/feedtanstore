<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = ['name', 'description', 'discount_percentage'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
