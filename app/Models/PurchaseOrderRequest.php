<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'product_id',
        'requested_quantity',
        'estimated_cost',
        'reason',
        'status',
        'requested_by',
        'approved_by',
        'supplier_id',
        'approved_at',
        'processed_at',
        'rejection_reason',
        'admin_notes',
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function generateRequestNumber()
    {
        $prefix = 'POR-';
        $date = now()->format('Ymd');
        $lastRequest = self::where('request_number', 'like', $prefix . $date . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->request_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $date . '-' . $newNumber;
    }
}
