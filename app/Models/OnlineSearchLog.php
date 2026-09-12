<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineSearchLog extends Model
{
    protected $fillable = ['search_term','search_term_normalized','results_count','customer_id','session_id','ip_address','was_found'];
    protected $casts = ['was_found'=>'boolean'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    protected static function booted(): void
    {
        static::saving(function($m){ $m->search_term_normalized = strtolower(trim($m->search_term)); });
    }
}
