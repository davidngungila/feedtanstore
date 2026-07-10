<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRider;
use App\Models\RiderLocation;
use App\Models\RiderReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    // Personal Info
    public function profile(Request $request)
    {
        $user = $request->user();
        $rider = $user->deliveryRider->load(['latestLocation']);
        return response()->json([
            'user' => $user,
            'rider' => $rider,
        ]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        $request->validate([
            'name' => 'nullable|string',
            'phone' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $rider->update($request->only(['name', 'phone', 'date_of_birth', 'gender', 'address']));
        return response()->json(['message' => 'Personal info updated', 'rider' => $rider]);
    }

    // Vehicle Details
    public function getVehicleDetails(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        return response()->json([
            'vehicle_type' => $rider->vehicle_type,
            'vehicle_plate' => $rider->vehicle_plate,
            'vehicle_model' => $rider->vehicle_model,
            'vehicle_color' => $rider->vehicle_color,
            'vehicle_year' => $rider->vehicle_year,
        ]);
    }

    public function updateVehicleDetails(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        $request->validate([
            'vehicle_type' => 'nullable|string',
            'vehicle_plate' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'vehicle_year' => 'nullable|string',
        ]);
        $rider->update($request->only(['vehicle_type', 'vehicle_plate', 'vehicle_model', 'vehicle_color', 'vehicle_year']));
        return response()->json(['message' => 'Vehicle details updated', 'rider' => $rider]);
    }

    // Documents
    public function getDocuments(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        return response()->json([
            'nid_number' => $rider->nid_number,
            'driving_license_number' => $rider->driving_license_number,
            'license_expiry_date' => $rider->license_expiry_date,
            'insurance_number' => $rider->insurance_number,
            'insurance_expiry_date' => $rider->insurance_expiry_date,
        ]);
    }

    public function updateDocuments(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        $request->validate([
            'nid_number' => 'nullable|string',
            'driving_license_number' => 'nullable|string',
            'license_expiry_date' => 'nullable|date',
            'insurance_number' => 'nullable|string',
            'insurance_expiry_date' => 'nullable|date',
        ]);
        $rider->update($request->only(['nid_number', 'driving_license_number', 'license_expiry_date', 'insurance_number', 'insurance_expiry_date']));
        return response()->json(['message' => 'Documents updated', 'rider' => $rider]);
    }

    // Bank Details
    public function getBankDetails(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        return response()->json([
            'bank_name' => $rider->bank_name,
            'bank_account_number' => $rider->bank_account_number,
            'bank_account_name' => $rider->bank_account_name,
            'bank_branch' => $rider->bank_branch,
            'mobile_money_number' => $rider->mobile_money_number,
            'mobile_money_provider' => $rider->mobile_money_provider,
        ]);
    }

    public function updateBankDetails(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        $request->validate([
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'mobile_money_number' => 'nullable|string',
            'mobile_money_provider' => 'nullable|string',
        ]);
        $rider->update($request->only(['bank_name', 'bank_account_number', 'bank_account_name', 'bank_branch', 'mobile_money_number', 'mobile_money_provider']));
        return response()->json(['message' => 'Bank details updated', 'rider' => $rider]);
    }

    // Performance Stats
    public function getPerformanceStats(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        
        $todayDeliveries = $rider->onlineOrders()
            ->whereDate('created_at', now()->today())
            ->where('status', 'delivered')
            ->count();
        
        $thisWeekDeliveries = $rider->onlineOrders()
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'delivered')
            ->count();
            
        $thisMonthDeliveries = $rider->onlineOrders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'delivered')
            ->count();
            
        return response()->json([
            'total_deliveries' => $rider->total_deliveries,
            'total_earnings' => $rider->total_earnings,
            'rating' => $rider->rating,
            'total_reviews' => $rider->total_reviews,
            'today_deliveries' => $todayDeliveries,
            'this_week_deliveries' => $thisWeekDeliveries,
            'this_month_deliveries' => $thisMonthDeliveries,
        ]);
    }

    // Customer Reviews
    public function getReviews(Request $request)
    {
        $rider = $request->user()->deliveryRider;
        $reviews = $rider->reviews()->paginate(10);
        return response()->json($reviews);
    }

    // Location
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