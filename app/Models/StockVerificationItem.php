<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockVerificationItem extends Model
{
    protected $fillable = [
        'session_id','product_id','system_quantity','physical_quantity','variance_quantity',
        'variance_percentage','variance_type','status','counted_at','counted_by','notes','variance_reason','adjustment_created'
    ];

    protected $casts = [
        'counted_at' => 'datetime',
        'adjustment_created' => 'boolean',
    ];

    public function session(): BelongsTo { return $this->belongsTo(StockVerificationSession::class,'session_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function counter(): BelongsTo { return $this->belongsTo(User::class, 'counted_by'); }

    public function calculateVariance(): void
    {
        if ($this->physical_quantity !== null) {
            $this->variance_quantity = $this->physical_quantity - $this->system_quantity;
            $this->variance_percentage = $this->system_quantity != 0 ? round(($this->variance_quantity / $this->system_quantity)*100,2) : ($this->physical_quantity>0?100:0);
            if ($this->variance_quantity < 0) $this->variance_type = 'shortage';
            elseif ($this->variance_quantity > 0) $this->variance_type = 'surplus';
            else $this->variance_type = 'matching';
        }
    }

    // Hide sensitive fields for auditor serialization
    public function toAuditorArray(): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->session_id,
            'product_id' => $this->product_id,
            'product' => $this->product,
            'physical_quantity' => $this->physical_quantity,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}
