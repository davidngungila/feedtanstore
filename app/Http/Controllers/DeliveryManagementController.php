<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\DeliveryRider;
use Illuminate\Http\Request;

class DeliveryManagementController extends Controller
{
    public function index()
    {
        $readyOrders = OnlineOrder::with(['items', 'rider'])->where('status', 'ready')->latest()->get();
        $outForDelivery = OnlineOrder::with(['items', 'rider'])->where('status', 'out_for_delivery')->latest()->get();
        $riders = DeliveryRider::all();
        return view('online.delivery', compact('readyOrders', 'outForDelivery', 'riders'));
    }

    public function map()
    {
        $activeOrders = OnlineOrder::with(['items', 'rider'])
            ->whereIn('status', ['ready', 'out_for_delivery'])
            ->whereNotNull('delivery_latitude')
            ->whereNotNull('delivery_longitude')
            ->latest()
            ->get();

        $riders = DeliveryRider::all();

        return view('online.delivery-map', compact('activeOrders', 'riders'));
    }

    public function customerMap()
    {
        $orders = OnlineOrder::with(['items', 'rider'])
            ->whereNotNull('delivery_latitude')
            ->whereNotNull('delivery_longitude')
            ->latest()
            ->get();

        return view('online.customer-locations', compact('orders'));
    }
}