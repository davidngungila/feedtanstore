<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model {
    protected $fillable = ['name', 'opening_balance', 'current_balance', 'is_active'];
}
