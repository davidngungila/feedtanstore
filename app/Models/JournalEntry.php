<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = ['entry_number', 'entry_date', 'description', 'reference_type', 'reference_number', 'is_posted', 'posted_by'];

    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
