<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverDailyReference extends Model
{
    protected $fillable = ['sales_rep_id','sales_date','driver_code','driver_name','delivery_rider_id','notes'];
    protected $casts = ['sales_date'=>'date'];

    public function salesRep(): BelongsTo { return $this->belongsTo(User::class, 'sales_rep_id'); }
    public function deliveryRider(): BelongsTo { return $this->belongsTo(DeliveryRider::class, 'delivery_rider_id'); }

    public function scopeToday($query, $salesRepId = null)
    {
        $q = $query->whereDate('sales_date', today());
        if ($salesRepId) $q->where('sales_rep_id', $salesRepId);
        return $q;
    }
}
