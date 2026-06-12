<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\DeliveryRider;
use Illuminate\Http\Request;

class DeliveryManagementController extends Controller
{
    public function index()
    {
        $readyOrders = OnlineOrder::with(['items', 'rider'])->where('status', 'Ready')->latest()->get();
        $outForDelivery = OnlineOrder::with(['items', 'rider'])->where('status', 'Out for Delivery')->latest()->get();
        $riders = DeliveryRider::all();
        return view('online.delivery', compact('readyOrders', 'outForDelivery', 'riders'));
    }
}