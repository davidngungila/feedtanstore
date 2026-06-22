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
                $nextCode = $lastShareholder ? self::incrementAlphanumeric(substr($lastShareholder->shareholding_number, -2)) : '01';
                $shareholder->shareholding_number = "FEEDTANSTORE-$year-$nextCode";
            }
        });
    }

    protected static function incrementAlphanumeric($code)
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($code);
        
        for ($i = $len - 1; $i >= 0; $i--) {
            $pos = strpos($chars, $code[$i]);
            if ($pos < strlen($chars) - 1) {
                $code[$i] = $chars[$pos + 1];
                return $code;
            } else {
                $code[$i] = $chars[0];
            }
        }
        
        return str_repeat($chars[0], $len + 1);
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
