<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    protected $fillable = ['name', 'email', 'phone', 'address', 'credit_limit', 'balance'];

    public function sales() {
        return $this->hasMany(Sale::class);
    }

    public function payments() {
        return $this->hasMany(CustomerPayment::class);
    }
}
