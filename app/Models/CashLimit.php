<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'max_cash_limit',
        'warning_threshold',
        'is_active',
    ];

    protected $casts = [
        'max_cash_limit' => 'decimal:2',
        'warning_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExceeded($currentAmount)
    {
        return $this->is_active && $currentAmount > $this->max_cash_limit;
    }

    public function shouldWarn($currentAmount)
    {
        return $this->is_active && $currentAmount >= $this->warning_threshold;
    }
}
