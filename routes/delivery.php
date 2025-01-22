<?php

use App\Http\Controllers\Delivery\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delivery\AuthController;
use App\Http\Controllers\Delivery\HomeController;
use App\Http\Controllers\Delivery\ProfileController;

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
    Route::get('/order/{id}', [OrderController::class,'show']);
    Route::post('/accept-order/{id}', [OrderController::class,'acceptOrder']);
    Route::post('/reject-order/{id}', [OrderController::class,'rejectOrder']);
    Route::post('/order-delivered/{id}', [OrderController::class,'orderDelivered']);
    Route::post('/cancel-order/{id}', [OrderController::class,'cancelOrder']);
    Route::get('/pending-orders', [OrderController::class,'pendingOrders']);
    Route::get('/completed-orders', [OrderController::class,'completedOrders']);

    //Home Routes
    Route::get('/search', [HomeController::class,'search']);
    Route::get('/branch-address', [HomeController::class,'branchAddress']);


});