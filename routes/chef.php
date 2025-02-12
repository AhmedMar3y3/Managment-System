<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chef\AuthController;
use App\Http\Controllers\Chef\ProfileController;
use App\Http\Controllers\Chef\OrderController;
use App\Http\Controllers\Chef\HomeController;


Route::post('/register',          [AuthController::class, 'register']);
Route::post('/verify',            [AuthController::class, 'verify']);
Route::post('/login',             [AuthController::class, 'login']);
Route::post('/forgot-password',   [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [AuthController::class, 'verifyResetCode']);
Route::post('/reset-password',    [AuthController::class, 'resetPassword']);

Route::middleware(['auth.chef'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::get('/profile',          [ProfileController::class, 'getProfile']);
    Route::post('/profile',         [ProfileController::class, 'updateProfile']);
    Route::post('/change-password', [ProfileController::class,'changePassword']);
    Route::delete('/delete-account',[ProfileController::class, 'deleteAccount']);

    // Home Routes
    Route::get('/banners',       [HomeController::class, 'banners']);
    Route::get('/new-orders',    [HomeController::class,'newOrders']);
    Route::get('/notifications', [HomeController::class, 'getNotifications']);
    
    //Order Routes
    Route::get('/orders/{id}',           [OrderController::class, 'getOrderDetails']);
    Route::get('/completed-orders',      [OrderController::class, 'completedOrders']);
    Route::get('/accepted-orders',       [OrderController::class, 'acceptedOrders']);
    Route::get('/pending-orders',        [OrderController::class,'pendingOrders']);
    Route::put('/accept-order/{id}',     [OrderController::class, 'acceptOrder']);
    Route::put('/order-in-progress/{id}',[OrderController::class, 'orderInProgress']);
    Route::put('/order-done/{id}',       [OrderController::class, 'orderDone']);
});