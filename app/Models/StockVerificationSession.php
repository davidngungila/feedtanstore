<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockVerificationSession extends Model
{
    protected $fillable = [
        'session_number','title','branch_id','location_id','status','assigned_auditor_id','created_by',
        'assigned_at','started_at','submitted_at','reviewed_at','reviewed_by','approved_at','approved_by',
        'notes','review_notes','total_products','counted_products','is_monthly_audit','audit_month'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_monthly_audit' => 'boolean',
        'audit_month' => 'date',
    ];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function auditor(): BelongsTo { return $this->belongsTo(User::class, 'assigned_auditor_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(StockVerificationItem::class, 'session_id'); }
    public function adjustments(): HasMany { return $this->hasMany(StockVerificationAdjustment::class, 'session_id'); }

    public static function generateNumber(): string
    {
        return 'SV-' . date('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }

    public function recalcCounts(): void
    {
        $this->update([
            'total_products' => $this->items()->count(),
            'counted_products' => $this->items()->where('status','counted')->count(),
        ]);
    }
}
