<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'product_id',
        'from_location_id',
        'to_location_id',
        'quantity',
        'status',
        'notes',
        'requested_by',
        'approved_by',
        'approved_at',
        'completed_at'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function isBulkTransfer()
    {
        return $this->items()->count() > 0;
    }

    public static function generateTransferNumber()
    {
        $prefix = 'ST-';
        $date = now()->format('Ymd');
        $lastTransfer = self::where('transfer_number', 'like', $prefix . $date . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastTransfer) {
            $lastNumber = (int) substr($lastTransfer->transfer_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $date . '-' . $newNumber;
    }
}
