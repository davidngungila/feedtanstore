<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\OnlineOrderController;
use App\Models\DeliveryRider;
use App\Models\OnlineOrder;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/catalog/products', [CatalogController::class, 'products']);
Route::get('/catalog/products/{id}', [CatalogController::class, 'product']);
Route::get('/catalog/carousel', [CatalogController::class, 'carousel']);
Route::get('/tracking/{orderNumber}', [TrackingController::class, 'trackOrder']);
Route::post('/payments/feedtan/callback', [OnlineOrderController::class, 'handlePaymentCallback'])->name('api.shop.payments.feedtan.callback');
Route::get('/terms-policies', [PublicController::class, 'termsAndPolicies']);
Route::get('/rider-support', [PublicController::class, 'riderSupport']);

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
    
    // Personal Info
    Route::put('/rider/personal-info', [RiderController::class, 'updatePersonalInfo']);
    
    // Vehicle Details
    Route::get('/rider/vehicle', [RiderController::class, 'getVehicleDetails']);
    Route::put('/rider/vehicle', [RiderController::class, 'updateVehicleDetails']);
    
    // Documents
    Route::get('/rider/documents', [RiderController::class, 'getDocuments']);
    Route::put('/rider/documents', [RiderController::class, 'updateDocuments']);
    
    // Bank Details
    Route::get('/rider/bank-details', [RiderController::class, 'getBankDetails']);
    Route::put('/rider/bank-details', [RiderController::class, 'updateBankDetails']);
    
    // Performance Stats
    Route::get('/rider/performance', [RiderController::class, 'getPerformanceStats']);
    
    // Customer Reviews
    Route::get('/rider/reviews', [RiderController::class, 'getReviews']);
    
    // Location
    Route::post('/rider/location', [RiderController::class, 'updateLocation']);
    Route::get('/rider/location/{riderId}', [RiderController::class, 'getLocation']);
    
    // Rider Order routes
    Route::get('/rider/orders', [OrderController::class, 'index']);
    Route::get('/rider/orders/available', [OrderController::class, 'available']);
    Route::get('/rider/orders/{id}', [OrderController::class, 'show']);
    Route::put('/rider/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/rider/orders/{id}/accept', [OrderController::class, 'accept']);
    Route::post('/rider/orders/{id}/reject', [OrderController::class, 'reject']);
});
