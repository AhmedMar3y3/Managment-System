<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales\AuthController;
use App\Http\Controllers\Sales\DeliveryController;
use App\Http\Controllers\Sales\OrderController;
use App\Http\Controllers\Sales\ProfileController;
use App\Http\Controllers\Sales\HomeController;
use App\Http\Controllers\Controller;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [AuthController::class, 'verifyResetCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware(['auth.sale'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile', [ProfileController::class, 'updateProfile']);
    Route::post('/change-password', [ProfileController::class,'changePassword']);
    Route::delete('/delete-account', [ProfileController::class, 'deleteAccount']);

    // Home Routes
    Route::get('/banners', [HomeController::class, 'banners']);
    Route::get('/ready-orders', [HomeController::class, 'readyOrders']);
    Route::get('/stats', [HomeController::class,'stats']);

    // Order Routes
    Route::get('/search', [OrderController::class, 'search']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/first-screen', [OrderController::class, 'storeFirstScreen']);
    Route::put('/orders/{order}/second-screen', [OrderController::class, 'storeSecondScreen']);
    Route::put('/orders/{order}/third-screen', [OrderController::class, 'storeThirdScreen']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::get('/new-orders', [OrderController::class, 'newOrders']);
    Route::get('/preparing-orders', [OrderController::class, 'preparingOrders']);
    Route::get('/delivered-orders', [OrderController::class, 'deliveredOrders']);

    // Delivery Routes
    Route::get('/deliveries', [DeliveryController::class,'deliveries']);
    Route::get('/deliveries/{id}', [DeliveryController::class,'show']);
    Route::post('/assign-to-delivery/{id}', [DeliveryController::class,'assignToDelivery']);

});
