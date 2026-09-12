<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionIssue extends Model
{
    protected $fillable = [
        'issue_number','sale_id','online_order_id','transaction_type','transaction_reference',
        'reporter_id','reported_at','issue_type','description','attachment','assigned_to',
        'status','resolution','resolved_by','resolved_at'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function onlineOrder(): BelongsTo { return $this->belongsTo(OnlineOrder::class); }
    public function reporter(): BelongsTo { return $this->belongsTo(User::class, 'reporter_id'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function resolver(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by'); }

    public static function generateNumber(): string
    {
        return 'ISS-' . date('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }
}
