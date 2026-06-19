<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model {
    protected $fillable = ['reference_number', 'reference_type', 'account', 'account_id', 'type', 'amount', 'description', 'journal_entry_id'];

    public function reference() {
        return $this->morphTo();
    }

    public function accountModel() {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function journalEntry() {
        return $this->belongsTo(JournalEntry::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (empty($entry->account_id)) {
                $account = Account::where('name', $entry->account)->first();
                if ($account) {
                    $entry->account_id = $account->id;
                }
            }
        });
    }
}
