<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model {
    protected $fillable = ['reference_number', 'date', 'category', 'description', 'amount', 'payment_method', 'bank_account_id', 'mobile_money_account_id', 'user_id'];
    
    protected $casts = [
        'date' => 'date',
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function bankAccount() {
        return $this->belongsTo(BankAccount::class);
    }
    
    public function mobileMoneyAccount() {
        return $this->belongsTo(MobileMoneyAccount::class);
    }
}
