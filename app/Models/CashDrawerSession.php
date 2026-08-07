<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawerSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_number',
        'user_id',
        'opening_balance',
        'cash_balance',
        'mobile_balance',
        'bank_balance',
        'online_balance',
        'opened_at',
        'closing_balance',
        'closed_at',
        'expected_balance',
        'difference',
        'status',
        'notes',
        'reconciled_by',
        'reconciled_at',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'cash_balance' => 'decimal:2',
        'mobile_balance' => 'decimal:2',
        'bank_balance' => 'decimal:2',
        'online_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'reconciled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reconciler()
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cash_drawer_session_id');
    }

    public static function generateSessionNumber()
    {
        $prefix = 'CDS-';
        $date = now()->format('Ymd');
        $lastSession = self::where('session_number', 'like', $prefix . $date . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastSession) {
            $lastNumber = (int) substr($lastSession->session_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $date . '-' . $newNumber;
    }

    public function isOpen()
    {
        return $this->status === 'opened';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isReconciled()
    {
        return $this->status === 'reconciled';
    }
}
