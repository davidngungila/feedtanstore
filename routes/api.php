<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\TrackingController;
use App\Models\DeliveryRider;
use App\Models\OnlineOrder;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/catalog/products', [CatalogController::class, 'products']);
Route::get('/catalog/products/{id}', [CatalogController::class, 'product']);
Route::get('/catalog/carousel', [CatalogController::class, 'carousel']);
Route::get('/tracking/{orderNumber}', [TrackingController::class, 'trackOrder']);

// Real-Time Data (Public)
Route::get('/realtime/riders', function () {
    $riders = DeliveryRider::with('latestLocation')->get();
    return response()->json($riders);
});
Route::get('/realtime/orders', function () {
    $orders = OnlineOrder::with(['rider', 'items.product'])->whereNotNull('delivery_latitude')->whereNotNull('delivery_longitude')->get();
    return response()->json($orders);
});

// Protected routes (Sanctum auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Rider routes
    Route::get('/rider/profile', [RiderController::class, 'profile']);
    Route::put('/rider/profile', [RiderController::class, 'updateProfile']);
    Route::post('/rider/location', [RiderController::class, 'updateLocation']);
    Route::get('/rider/location/{riderId}', [RiderController::class, 'getLocation']);
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/available', [OrderController::class, 'available']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/accept', [OrderController::class, 'accept']);
});