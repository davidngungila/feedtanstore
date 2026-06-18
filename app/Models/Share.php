<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Share extends Model
{
    protected $fillable = [
        'shareholder_id',
        'number_of_shares',
        'share_price',
        'total_amount',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function shareholder(): BelongsTo
    {
        return $this->belongsTo(Shareholder::class);
    }
}
