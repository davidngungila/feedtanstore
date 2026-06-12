<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model {
    protected $fillable = ['user_id', 'opened_at', 'closed_at', 'opening_cash', 'closing_cash', 'cash_sales', 'card_sales', 'mobile_sales', 'notes'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function sales() {
        return $this->hasMany(Sale::class);
    }
}
