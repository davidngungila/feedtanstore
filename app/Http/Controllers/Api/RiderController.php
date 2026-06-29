<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRider;
use App\Models\RiderLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        $rider = $user->deliveryRider->load(['locations' => function($q) {
            $q->latest()->take(1);
        }]);
        return response()->json([
            'user' => $user,
            'rider' => $rider,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        $request->validate([
            'name' => 'nullable|string',
            'phone' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'vehicle_plate' => 'nullable|string',
        ]);
        $rider->update($request->only(['name', 'phone', 'vehicle_type', 'vehicle_plate']));
        return response()->json(['message' => 'Profile updated', 'rider' => $rider]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $rider = $request->user()->deliveryRider;

        if (!$rider) {
            return response()->json(['message' => 'Rider not found'], 404);
        }

        RiderLocation::create([
            'delivery_rider_id' => $rider->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['message' => 'Location updated']);
    }

    public function getLocation($riderId)
    {
        $location = RiderLocation::where('delivery_rider_id', $riderId)->latest()->first();
        return response()->json($location);
    }
}