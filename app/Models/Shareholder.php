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
                $nextNumber = $lastShareholder ? (self::base36ToInt(substr($lastShareholder->shareholding_number, -2)) + 1) : 0;
                $shareholder->shareholding_number = "FEEDTANSTORE-$year-" . str_pad(self::intToBase36($nextNumber), 2, '0', STR_PAD_LEFT);
            }
        });
    }

    protected static function intToBase36(int $num): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($num == 0) return '0';
        $result = '';
        while ($num > 0) {
            $result = $chars[$num % 36] . $result;
            $num = floor($num / 36);
        }
        return $result;
    }

    protected static function base36ToInt(string $str): int
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = 0;
        $str = strtoupper($str);
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            $pos = strpos($chars, $char);
            $result = $result * 36 + $pos;
        }
        return $result;
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
