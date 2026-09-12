<?php

use App\Http\Controllers\Api\AppNotificationController;
use App\Http\Controllers\Api\AttendanceAuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AttendanceProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\DispatchRequestController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\TrackingSessionController;
use App\Http\Controllers\OnlineOrderController;
use App\Models\DeliveryRider;
use App\Models\OnlineOrder;
use Illuminate\Support\Facades\Route;

// ===================== ATTENDANCE MVP PUBLIC ROUTES =====================
Route::prefix('attendance')->group(function () {
    Route::post('/login', [AttendanceAuthController::class, 'login']);
    Route::post('/forgot-password', [AttendanceAuthController::class, 'forgotPassword']);
});

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/catalog/products', [CatalogController::class, 'products']);
Route::get('/catalog/products/{id}', [CatalogController::class, 'product']);
Route::get('/catalog/carousel', [CatalogController::class, 'carousel']);
Route::get('/tracking/{orderNumber}', [TrackingController::class, 'trackOrder']);
Route::get('/tracking/order/{orderNumber}', [TrackingSessionController::class, 'byOrder']);
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

// ===================== ATTENDANCE MVP PROTECTED ROUTES =====================
Route::middleware('auth:sanctum')->prefix('attendance')->group(function () {
    Route::post('/logout', [AttendanceAuthController::class, 'logout']);
    Route::get('/me', [AttendanceAuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [AttendanceController::class, 'dashboard']);
    Route::get('/today', [AttendanceController::class, 'today']);
    Route::get('/stats', [AttendanceController::class, 'stats']);

    // Check In / Check Out
    Route::post('/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/check-out', [AttendanceController::class, 'checkOut']);

    // Attendance History & Calendar
    Route::get('/history', [AttendanceController::class, 'history']);
    Route::get('/calendar', [AttendanceController::class, 'calendar']);

    // Profile
    Route::get('/profile', [AttendanceProfileController::class, 'show']);
    Route::put('/profile', [AttendanceProfileController::class, 'update']);
    Route::post('/profile/photo', [AttendanceProfileController::class, 'updatePhoto']);
    Route::post('/profile/change-password', [AttendanceProfileController::class, 'changePassword']);

    // Leave Management
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    Route::get('/leaves/{id}', [LeaveController::class, 'show']);
    Route::post('/leaves/{id}/cancel', [LeaveController::class, 'cancel']);
    Route::post('/leaves/{id}/review', [LeaveController::class, 'review']); // manager/admin

    // Notifications
    Route::get('/notifications', [AppNotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [AppNotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [AppNotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [AppNotificationController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [AppNotificationController::class, 'destroy']);
    Route::delete('/notifications/clear/read', [AppNotificationController::class, 'clearRead']);
});

// Protected routes (Sanctum auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Push notification device token management
    Route::post('/device-token', [DeviceTokenController::class, 'store']);
    Route::delete('/device-token', [DeviceTokenController::class, 'destroy']);
    Route::post('/rider/device-token', [DeviceTokenController::class, 'store']);
    
    // Rider routes
    Route::get('/rider/profile', [RiderController::class, 'profile']);
    Route::post('/rider/profile-image', [RiderController::class, 'updateProfileImage']);

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

    // Rider Dispatch Requests (broadcast requests sent by the marketing officer)
    Route::get('/rider/dispatch-requests', [DispatchRequestController::class, 'index']);
    Route::post('/rider/dispatch-requests/{id}/accept', [DispatchRequestController::class, 'accept']);
    Route::post('/rider/dispatch-requests/{id}/decline', [DispatchRequestController::class, 'decline']);

    // Bulk dispatch batches (multiple nearby orders grouped into one offer)
    Route::get('/rider/dispatch-batches', [DispatchRequestController::class, 'batches']);
    Route::post('/rider/dispatch-batches/{id}/accept', [DispatchRequestController::class, 'acceptBatch']);
    Route::post('/rider/dispatch-batches/{id}/decline', [DispatchRequestController::class, 'declineBatch']);

    // Live trip tracking (Bolt/Uber style)
    Route::post('/tracking/location', [TrackingSessionController::class, 'storeLocation'])
        ->middleware('throttle:tracking-location');
    Route::post('/tracking/presence', [TrackingSessionController::class, 'presence'])
        ->middleware('throttle:tracking-api');
    Route::get('/tracking/sessions', [TrackingSessionController::class, 'index'])
        ->middleware('throttle:tracking-api');
    Route::get('/tracking/sessions/{id}', [TrackingSessionController::class, 'show'])
        ->middleware('throttle:tracking-api');
    Route::post('/tracking/sessions/{id}/status', [TrackingSessionController::class, 'updateStatus'])
        ->middleware('throttle:tracking-api');
    Route::post('/tracking/sessions/{id}/route', [TrackingSessionController::class, 'route'])
        ->middleware('throttle:tracking-api');

    // ===================== FIELD SALES MOBILE APP =====================
    Route::prefix('field-sales')->group(function(){
        Route::get('/dashboard', [\App\Http\Controllers\Api\FieldSalesController::class,'dashboard']);
        Route::get('/products', [\App\Http\Controllers\Api\FieldSalesController::class,'products']);
        Route::get('/products/{product}/availability', [\App\Http\Controllers\Api\FieldSalesController::class,'checkAvailability']);
        Route::get('/customers', [\App\Http\Controllers\Api\FieldSalesController::class,'customers']);
        Route::post('/customers', [\App\Http\Controllers\Api\FieldSalesController::class,'storeCustomer']);
        Route::post('/orders', [\App\Http\Controllers\Api\FieldSalesController::class,'createOrder']);
        Route::get('/orders', [\App\Http\Controllers\Api\FieldSalesController::class,'myOrders']);
        Route::get('/orders/{order}', [\App\Http\Controllers\Api\FieldSalesController::class,'showOrder']);
        Route::put('/orders/{order}/status', [\App\Http\Controllers\Api\FieldSalesController::class,'updateOrderStatus']);
        Route::post('/orders/sync-offline', [\App\Http\Controllers\Api\FieldSalesController::class,'syncOffline']);
        // Driver / Reference Code for daily sales
        Route::post('/driver-code', [\App\Http\Controllers\Api\FieldSalesController::class,'setDriverCode']);
        Route::get('/driver-code', [\App\Http\Controllers\Api\FieldSalesController::class,'getDriverCode']);
        Route::get('/sales/by-driver', [\App\Http\Controllers\Api\FieldSalesController::class,'salesByDriverAndDate']);
        // Demand & competitor via field sales
        Route::post('/demands', [\App\Http\Controllers\CustomerDemandController::class,'apiStore']);
        Route::get('/demands', [\App\Http\Controllers\CustomerDemandController::class,'apiIndex']);
        Route::post('/competitor-intel', [\App\Http\Controllers\CompetitorIntelligenceController::class,'apiStore']);
        Route::get('/competitor-intel', [\App\Http\Controllers\CompetitorIntelligenceController::class,'apiIndex']);
    });

    // Stock Verification (auditor blind)
    Route::prefix('stock-verification')->group(function(){
        Route::get('/assigned', [\App\Http\Controllers\StockVerificationController::class,'apiAssignedSessions']);
        Route::get('/{session}', [\App\Http\Controllers\StockVerificationController::class,'apiSessionDetail']);
        Route::post('/{session}/submit', [\App\Http\Controllers\StockVerificationController::class,'apiSubmit']);
    });

    // POS offline sync
    Route::prefix('offline')->group(function(){
        Route::post('/queue', [\App\Http\Controllers\OfflineSyncController::class,'queue']);
        Route::post('/sync', [\App\Http\Controllers\OfflineSyncController::class,'sync']);
        Route::get('/status', [\App\Http\Controllers\OfflineSyncController::class,'status']);
    });

    // Customer demand / competitor / ratings / issues / cashier performance (mobile + POS)
    Route::post('/customer-demands', [\App\Http\Controllers\CustomerDemandController::class,'apiStore']);
    Route::post('/competitor-intel', [\App\Http\Controllers\CompetitorIntelligenceController::class,'apiStore']);
    Route::post('/customer-ratings', [\App\Http\Controllers\CustomerRatingController::class,'apiStore']);
    Route::post('/transaction-issues', [\App\Http\Controllers\TransactionIssueController::class,'apiStore']);
    Route::post('/cashier-service-time/start', [\App\Http\Controllers\CashierPerformanceController::class,'start']);
    Route::post('/cashier-service-time/end', [\App\Http\Controllers\CashierPerformanceController::class,'end']);

    // Product search for field sales & POS
    Route::get('/products/search', function(\Illuminate\Http\Request $r){
        $q=\App\Models\Product::where('is_active',true);
        if($r->filled('term')) $q->where('name','like','%'.$r->term.'%')->orWhere('barcode','like','%'.$r->term.'%');
        return response()->json($q->with(['category'])->limit(50)->get());
    });
});
