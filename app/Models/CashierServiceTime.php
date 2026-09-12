<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierServiceTime extends Model
{
    protected $fillable = [
        'sale_id','online_order_id','cashier_id','branch_id','location_id','customer_id',
        'service_start_time','service_end_time','duration_seconds','status','channel','start_trigger'
    ];

    protected $casts = [
        'service_start_time' => 'datetime',
        'service_end_time' => 'datetime',
    ];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function cashier(): BelongsTo { return $this->belongsTo(User::class,'cashier_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }

    public function complete(): void
    {
        $this->service_end_time = now();
        $this->duration_seconds = $this->service_start_time->diffInSeconds($this->service_end_time);
        $this->status = 'completed';
        $this->save();
    }

    public static function startForCashier(int $cashierId, ?int $customerId = null, ?int $branchId = null, string $trigger='new_sale'): self
    {
        return self::create([
            'cashier_id' => $cashierId,
            'customer_id' => $customerId,
            'branch_id' => $branchId,
            'service_start_time' => now(),
            'status' => 'active',
            'channel' => 'in_store',
            'start_trigger' => $trigger,
        ]);
    }
}
