<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineTransaction extends Model
{
    protected $fillable = [
        'local_transaction_id','transaction_type','payload','cashier_id','branch_id','sync_status',
        'sync_attempts','last_error','synced_sale_id','offline_created_at','synced_at','device_info','ip_address'
    ];

    protected $casts = [
        'payload'=>'array',
        'offline_created_at'=>'datetime',
        'synced_at'=>'datetime',
    ];

    public function cashier(): BelongsTo { return $this->belongsTo(User::class,'cashier_id'); }
    public function syncedSale(): BelongsTo { return $this->belongsTo(Sale::class,'synced_sale_id'); }
}
