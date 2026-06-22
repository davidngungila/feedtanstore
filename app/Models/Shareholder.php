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
        'shareholding_number',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shareholder) {
            if (!$shareholder->shareholding_number) {
                $year = date('y');
                $lastShareholder = self::where('shareholding_number', 'like', "FEEDTANSTORE-$year-%")->orderBy('id', 'desc')->first();
                $nextNumber = $lastShareholder ? (int)substr($lastShareholder->shareholding_number, -2) + 1 : 1;
                $shareholder->shareholding_number = "FEEDTANSTORE-$year-" . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
            }
        });
    }

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
