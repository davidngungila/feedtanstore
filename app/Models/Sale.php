<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model {
    protected $fillable = ['invoice_number', 'customer_id', 'user_id', 'shift_id', 'discount_id', 'subtotal', 'tax', 'discount', 'total', 'paid', 'change', 'payment_method', 'type', 'status', 'notes'];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function shift() {
        return $this->belongsTo(Shift::class);
    }

    public function discountApplied() {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function items() {
        return $this->hasMany(SaleItem::class);
    }

    public function returns() {
        return $this->hasMany(SaleReturn::class);
    }

    public function accountingEntries() {
        return $this->morphMany(AccountingEntry::class, 'reference');
    }
}
