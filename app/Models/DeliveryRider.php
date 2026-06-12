<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRider extends Model {
    protected $fillable = ['name', 'phone', 'vehicle_type', 'vehicle_plate', 'is_active'];
}
