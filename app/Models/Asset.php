<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'purchase_date',
        'depreciation_start_date',
        'purchase_cost',
        'salvage_value',
        'accumulated_depreciation',
        'last_depreciation_date',
        'useful_life_years',
        'depreciation_method',
        'location',
        'status',
        'serial_number',
        'manufacturer',
        'model',
        'warranty_expiry',
        'assigned_to',
        'maintenance_notes',
        'user_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'depreciation_start_date' => 'date',
        'last_depreciation_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'warranty_expiry' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calculate straight-line depreciation per year
    public function getAnnualDepreciationAttribute(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0;
        }
        
        return match($this->depreciation_method) {
            'straight_line' => ($this->purchase_cost - $this->salvage_value) / $this->useful_life_years,
            'declining_balance' => $this->calculateDecliningBalanceDepreciation(),
            'double_declining_balance' => $this->calculateDoubleDecliningBalanceDepreciation(),
            default => ($this->purchase_cost - $this->salvage_value) / $this->useful_life_years,
        };
    }

    // Calculate declining balance depreciation
    private function calculateDecliningBalanceDepreciation(): float
    {
        $rate = 1 / $this->useful_life_years;
        $currentValue = $this->current_value;
        return $currentValue * $rate;
    }

    // Calculate double declining balance depreciation
    private function calculateDoubleDecliningBalanceDepreciation(): float
    {
        $rate = 2 / $this->useful_life_years;
        $currentValue = $this->current_value;
        $depreciation = $currentValue * $rate;
        
        // Ensure depreciation doesn't reduce value below salvage value
        if ($currentValue - $depreciation < $this->salvage_value) {
            return $currentValue - $this->salvage_value;
        }
        
        return $depreciation;
    }

    // Calculate current book value
    public function getCurrentValueAttribute(): float
    {
        $startDate = $this->depreciation_start_date ?? $this->purchase_date;
        $yearsUsed = now()->diffInYears($startDate);
        
        if ($yearsUsed >= $this->useful_life_years) {
            return $this->salvage_value;
        }

        $totalDepreciation = match($this->depreciation_method) {
            'straight_line' => $this->annual_depreciation * $yearsUsed,
            'declining_balance' => $this->calculateDecliningBalanceTotalDepreciation($yearsUsed),
            'double_declining_balance' => $this->calculateDoubleDecliningBalanceTotalDepreciation($yearsUsed),
            default => $this->annual_depreciation * $yearsUsed,
        };

        return max($this->purchase_cost - $totalDepreciation, $this->salvage_value);
    }

    // Calculate total declining balance depreciation
    private function calculateDecliningBalanceTotalDepreciation(int $yearsUsed): float
    {
        $rate = 1 / $this->useful_life_years;
        $value = $this->purchase_cost;
        $totalDepreciation = 0;

        for ($i = 0; $i < $yearsUsed; $i++) {
            $depreciation = $value * $rate;
            $totalDepreciation += $depreciation;
            $value -= $depreciation;
            
            if ($value <= $this->salvage_value) {
                break;
            }
        }

        return $totalDepreciation;
    }

    // Calculate total double declining balance depreciation
    private function calculateDoubleDecliningBalanceTotalDepreciation(int $yearsUsed): float
    {
        $rate = 2 / $this->useful_life_years;
        $value = $this->purchase_cost;
        $totalDepreciation = 0;

        for ($i = 0; $i < $yearsUsed; $i++) {
            $depreciation = $value * $rate;
            
            // Switch to straight-line when it becomes more beneficial
            $remainingLife = $this->useful_life_years - $i;
            $straightLineDepreciation = ($value - $this->salvage_value) / $remainingLife;
            
            if ($straightLineDepreciation > $depreciation) {
                $depreciation = $straightLineDepreciation;
            }
            
            $totalDepreciation += $depreciation;
            $value -= $depreciation;
            
            if ($value <= $this->salvage_value) {
                break;
            }
        }

        return $totalDepreciation;
    }

    // Calculate accumulated depreciation
    public function getAccumulatedDepreciationAttribute(): float
    {
        return $this->purchase_cost - $this->current_value;
    }

    // Get depreciation percentage
    public function getDepreciationPercentageAttribute(): float
    {
        if ($this->purchase_cost == 0) {
            return 0;
        }
        return ($this->accumulated_depreciation / $this->purchase_cost) * 100;
    }

    // Check if asset is fully depreciated
    public function getIsFullyDepreciatedAttribute(): bool
    {
        return $this->current_value <= $this->salvage_value;
    }

    // Get remaining useful life in years
    public function getRemainingLifeAttribute(): int
    {
        $startDate = $this->depreciation_start_date ?? $this->purchase_date;
        $yearsUsed = now()->diffInYears($startDate);
        return max(0, $this->useful_life_years - $yearsUsed);
    }

    // Get projected depreciation schedule
    public function getDepreciationScheduleAttribute(): array
    {
        $schedule = [];
        $startDate = $this->depreciation_start_date ?? $this->purchase_date;
        $bookValue = $this->purchase_cost;
        $accumulatedDepreciation = 0;

        for ($year = 1; $year <= $this->useful_life_years; $year++) {
            $yearDate = $startDate->copy()->addYears($year);
            
            $depreciation = match($this->depreciation_method) {
                'straight_line' => ($this->purchase_cost - $this->salvage_value) / $this->useful_life_years,
                'declining_balance' => $this->calculateScheduleDecliningBalance($bookValue, $year),
                'double_declining_balance' => $this->calculateScheduleDoubleDeclining($bookValue, $year),
                default => ($this->purchase_cost - $this->salvage_value) / $this->useful_life_years,
            };

            $accumulatedDepreciation += $depreciation;
            $bookValue = max($this->purchase_cost - $accumulatedDepreciation, $this->salvage_value);

            $schedule[] = [
                'year' => $year,
                'date' => $yearDate->format('Y-m-d'),
                'depreciation' => $depreciation,
                'accumulated_depreciation' => $accumulatedDepreciation,
                'book_value' => $bookValue,
            ];
        }

        return $schedule;
    }

    // Helper for schedule declining balance calculation
    private function calculateScheduleDecliningBalance(float $bookValue, int $year): float
    {
        $rate = 1 / $this->useful_life_years;
        $depreciation = $bookValue * $rate;
        
        if ($bookValue - $depreciation < $this->salvage_value) {
            return $bookValue - $this->salvage_value;
        }
        
        return $depreciation;
    }

    // Helper for schedule double declining balance calculation
    private function calculateScheduleDoubleDeclining(float $bookValue, int $year): float
    {
        $rate = 2 / $this->useful_life_years;
        $depreciation = $bookValue * $rate;
        
        $remainingLife = $this->useful_life_years - $year + 1;
        $straightLineDepreciation = ($bookValue - $this->salvage_value) / $remainingLife;
        
        if ($straightLineDepreciation > $depreciation) {
            $depreciation = $straightLineDepreciation;
        }
        
        if ($bookValue - $depreciation < $this->salvage_value) {
            return $bookValue - $this->salvage_value;
        }
        
        return $depreciation;
    }
}
