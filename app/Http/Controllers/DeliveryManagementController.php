<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\DeliveryRider;
use Illuminate\Http\Request;

class DeliveryManagementController extends Controller
{
    public function index()
    {
        $pendingOrders = OnlineOrder::where('status', 'ready')->with(['items', 'customer'])->get();
        $outForDelivery = OnlineOrder::where('status', 'out_for_delivery')->with(['rider', 'items'])->get();
        $riders = DeliveryRider::where('is_active', true)->get();
        return view('online.delivery', compact('pendingOrders', 'outForDelivery', 'riders'));
    }
}
