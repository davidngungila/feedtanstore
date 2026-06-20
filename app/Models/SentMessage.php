<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentMessage extends Model
{
    protected $fillable = [
        'type',
        'to',
        'from',
        'subject',
        'message',
        'api_response',
        'status',
        'message_id',
    ];

    protected $casts = [
        'api_response' => 'array',
    ];
}
