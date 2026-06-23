<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/catalog/products', [CatalogController::class, 'products']);
Route::get('/catalog/products/{id}', [CatalogController::class, 'product']);
Route::get('/catalog/carousel', [CatalogController::class, 'carousel']);
Route::get('/tracking/{orderNumber}', [TrackingController::class, 'trackOrder']);

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