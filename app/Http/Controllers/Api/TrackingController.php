<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineOrder;
use App\Models\RiderLocation;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function trackOrder($orderNumber)
    {
        $order = OnlineOrder::where('order_number', $orderNumber)
            ->with(['items.product', 'rider', 'statusHistory'])
            ->firstOrFail();
        
        $riderLocation = null;
        if ($order->rider) {
            $riderLocation = RiderLocation::where('delivery_rider_id', $order->rider->id)
                ->latest()
                ->first();
        }

        return response()->json([
            'order' => $order,
            'rider' => $order->rider,
            'current_location' => $riderLocation,
        ]);
    }
}