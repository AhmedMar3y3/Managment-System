<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delivery\AuthController;
use App\Http\Controllers\Delivery\HomeController;
use App\Http\Controllers\Delivery\OrderController;
use App\Http\Controllers\Delivery\ProfileController;
use App\Http\Controllers\Delivery\TrackingController;
use App\Http\Controllers\Delivery\OrderManipulationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [AuthController::class, 'verifyResetCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::middleware(['auth.delivery'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::delete('/delete-account', [ProfileController::class, 'deleteAccount']);

    // Order Routes
    Route::get('/new-orders', [OrderController::class,'newOrders']);
    Route::get('/returned-orders', [OrderController::class,'returnedOrders']);
    Route::get('/pending-orders', [OrderController::class,'pendingOrders']);
    Route::get('/completed-orders', [OrderController::class,'completedOrders']);
    Route::get('/order/{id}', [OrderController::class,'show']);

    // Order Manipulation Routes
    Route::post('/accept-order/{id}', [OrderManipulationController::class,'acceptOrder']);
    Route::post('/reject-order/{id}', [OrderManipulationController::class,'rejectOrder']);
    Route::post('/order-delivered/{id}', [OrderManipulationController::class,'orderDelivered']);
    Route::post('/cancel-order/{id}', [OrderManipulationController::class,'cancelOrder']);

    //Home Routes
    Route::get('/search', [HomeController::class,'search']);
    Route::get('/branch-address', [HomeController::class,'branchAddress']);

    //Tracking Routes
    Route::post('store',                     [TrackingController::class,'store']);
    Route::get('latest-position', [TrackingController::class,'latest']);


});