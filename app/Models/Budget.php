<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model {
    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'total_amount', 'category'];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
    ];
    
    public function expenses() {
        return $this->hasMany(Expense::class);
    }
    
    public function getSpentAmountAttribute() {
        return $this->expenses()->sum('amount');
    }
    
    public function getRemainingAmountAttribute() {
        return $this->total_amount - $this->spent_amount;
    }
    
    public function getUtilizationPercentageAttribute() {
        if ($this->total_amount <= 0) return 0;
        return min(100, round(($this->spent_amount / $this->total_amount) * 100, 2));
    }
}
