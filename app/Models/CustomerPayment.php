<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model {
    protected $fillable = ['payment_number', 'customer_id', 'user_id', 'amount', 'payment_method', 'notes'];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
