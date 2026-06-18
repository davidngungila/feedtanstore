<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shareholder extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function getTotalSharesAttribute()
    {
        return $this->shares()->sum('number_of_shares');
    }

    public function getTotalInvestmentAttribute()
    {
        return $this->shares()->sum('total_amount');
    }
}
