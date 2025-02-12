<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\DeliveriesController;
use App\Http\Controllers\Manager\ChefController;
use App\Http\Controllers\Manager\EmployeeController;
use App\Http\Controllers\Manager\HomeController;
use App\Http\Controllers\Manager\OrderManipulationController;
use App\Http\Controllers\Manager\ProfileController;
use App\Http\Controllers\Manager\TrackingPositionController;
use App\Http\Controllers\Manager\OrderController;



// Public Routes
Route::post('login',           [AuthController::class, 'login']);
Route::post('register',        [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-code',     [AuthController::class, 'verifyCode']);
Route::post('reset-password',  [AuthController::class, 'resetPassword']);
Route::post('verify',          [AuthController::class, 'verify']);

Route::middleware('auth.manager')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::get('/profile',           [ProfileController::class, 'getProfilemanager']);
    Route::post('/profile',          [ProfileController::class, 'updateProfilemanager']);
    Route::post('/change-password',  [ProfileController::class,'changePassword']);
    Route::delete('/delete-account', [ProfileController::class, 'deleteAccountmanager']);

    // Home Routes
    Route::get('/stats',               [HomeController::class, 'stats']);
    Route::get('/new-orders',          [HomeController::class, 'NewOrders']);
    Route::get('/show-new-order/{id}', [HomeController::class, 'ShowNewOrder']);
    Route::get('/inprogress-orders',   [HomeController::class, 'inProgressOrders']);

    // Order Routes
    Route::get('/not-assigned-orders', [OrderController::class,'managerAcceptedOrders']);
    Route::get('/completed-orders',    [OrderController::class, 'completedOrders']);
    Route::get('/delivered-orders',    [OrderController::class, 'deliveredOrders']);
    Route::get('/show-order/{id}',     [OrderController::class, 'show']);
    Route::get('/rejected-orders',     [OrderController::class, 'deliveryRejectedOrders']);
    Route::get('/returned-orders',     [OrderController::class, 'returnedOrders']);

    // Order Maniuplation Routes
    Route::post('/accept-order/{id}',     [OrderManipulationController::class, 'acceptOrder']);
    Route::post('/assign-to-Chef',        [OrderManipulationController::class, 'assignToChef']);
    Route::post('/assign-order-delivery', [OrderManipulationController::class, 'assignOrderToDelivery']);

    // Emolyees Routes
    Route::get('/chefs',         [ChefController::class, 'chefs']);
    Route::get('/chef/{id}',     [ChefController::class, 'showChef']);
    Route::get('/deliveries',         [DeliveriesController::class, 'AllDeliveries']);
    Route::get('/show-delivery/{id}', [DeliveriesController::class, 'showDelivery']);

    //  New Requests Routes
    Route::get('/all-requests',          [EmployeeController::class, 'Addition']);
    Route::post('/accept-chef/{id}',     [EmployeeController::class, 'acceptChef']);
    Route::post('/reject-chef/{id}',     [EmployeeController::class, 'rejectChef']);
    Route::post('/accept-delivery/{id}', [EmployeeController::class, 'acceptDelivery']);
    Route::post('/reject-delivery/{id}', [EmployeeController::class, 'rejectDelivery']);

    // Tracking Routes
    Route::post('/store-tracking',      [TrackingPositionController::class, 'store']);
    Route::get('/show-latest/{id}',     [TrackingPositionController::class, 'latest']);
    Route::get('/orders-with-delivery', [TrackingPositionController::class, 'orderWithDelivery']);
});
