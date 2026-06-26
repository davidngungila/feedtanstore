<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAccessToken extends Model
{
    protected $fillable = [
        'token_hash',
        'encrypted_token',
        'expires_at',
        'used_at',
        'used_ip',
        'used_user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
}
