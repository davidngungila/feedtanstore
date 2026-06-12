<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model {
    protected $fillable = ['name', 'account_number', 'bank_name', 'branch', 'balance', 'is_active'];
}
