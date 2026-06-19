<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_number',
        'entry_date',
        'description',
        'reference_type',
        'reference_id',
        'is_manual',
    ];

    protected $dates = ['entry_date'];

    public function entries()
    {
        return $this->hasMany(AccountingEntry::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function getTotalDebitsAttribute()
    {
        return $this->entries()->where('type', 'debit')->sum('amount');
    }

    public function getTotalCreditsAttribute()
    {
        return $this->entries()->where('type', 'credit')->sum('amount');
    }

    public function getIsBalancedAttribute()
    {
        return $this->total_debits == $this->total_credits;
    }
}
