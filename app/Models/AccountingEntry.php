<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model {
    protected $fillable = ['reference_number', 'reference_type', 'account', 'account_id', 'type', 'amount', 'description'];

    public function reference() {
        return $this->morphTo();
    }

    public function accountModel() {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
