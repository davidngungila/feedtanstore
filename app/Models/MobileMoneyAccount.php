<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileMoneyAccount extends Model {
    protected $fillable = ['provider', 'phone_number', 'account_name', 'balance', 'is_active'];
}
