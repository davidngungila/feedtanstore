<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\DeliveryRider;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $settings = StoreSetting::firstOrCreate();
        
        // Get store location (default to Arusha, Tanzania if not set)
        $storeLat = $settings->store_latitude ?? -3.3869; 
        $storeLng = $settings->store_longitude ?? 36.6883;
        
        $routes = [];
        
        if ($settings->openrouteservice_api_key) {
            foreach ($activeOrders as $order) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => $settings->openrouteservice_api_key,
                        'Content-Type' => 'application/json'
                    ])->post('https://api.openrouteservice.org/v2/directions/driving-car/geojson', [
                        'coordinates' => [
                            [$storeLng, $storeLat],
                            [$order->delivery_longitude, $order->delivery_latitude]
                        ]
                    ]);
                    
                    if ($response->successful()) {
                        $routes[$order->id] = $response->json();
                    }
                } catch (\Exception $e) {
                    // Log error or ignore
                }
            }
        }

        return view('online.delivery-map', compact('activeOrders', 'riders', 'routes', 'storeLat', 'storeLng'));
    }

    public function customerMap()
    {
        $orders = OnlineOrder::with(['items', 'rider'])
            ->whereNotNull('delivery_latitude')
            ->whereNotNull('delivery_longitude')
            ->latest()
            ->get();

        $settings = StoreSetting::firstOrCreate();
        
        // Get store location (default to Arusha, Tanzania if not set)
        $storeLat = $settings->store_latitude ?? -3.3869;
        $storeLng = $settings->store_longitude ?? 36.6883;
        
        $routes = [];
        
        if ($settings->openrouteservice_api_key) {
            foreach ($orders as $order) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => $settings->openrouteservice_api_key,
                        'Content-Type' => 'application/json'
                    ])->post('https://api.openrouteservice.org/v2/directions/driving-car/geojson', [
                        'coordinates' => [
                            [$storeLng, $storeLat],
                            [$order->delivery_longitude, $order->delivery_latitude]
                        ]
                    ]);
                    
                    if ($response->successful()) {
                        $routes[$order->id] = $response->json();
                    }
                } catch (\Exception $e) {
                    // Log error or ignore
                }
            }
        }

        return view('online.customer-locations', compact('orders', 'routes', 'storeLat', 'storeLng'));
    }
}