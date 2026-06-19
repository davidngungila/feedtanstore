<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = ['account_code', 'name', 'type', 'description', 'is_active', 'parent_id'];

    protected $table = 'chart_of_accounts';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function accountingEntries(): HasMany
    {
        return $this->hasMany(AccountingEntry::class, 'account', 'name');
    }
}
