<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\OrdersDeliveredController;
use App\Http\Controllers\Manager\DeliveriesController;
use App\Http\Controllers\Manager\ChefController;
use App\Http\Controllers\Manager\OrdersNotFinishedController;
use App\Http\Controllers\Manager\OrdersCompletedController;
use App\Http\Controllers\Manager\AdditionRequestsController;
use App\Http\Controllers\Manager\ProfileManagerController;
use App\Http\Controllers\Manager\TrackingPositionController;
use App\Http\Controllers\Manager\RejectedOrdersController;
use App\Http\Controllers\Manager\ReturnOrdersController;



// Public Routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-code', [AuthController::class, 'verifyCode']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('verify', [AuthController::class, 'verify']);

    Route::middleware('auth.manager')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::get('/profile', [ProfileManagerController::class, 'getProfilemanager']);
    Route::put('/profile', [ProfileManagerController::class, 'updateProfilemanager']);
    Route::delete('/delete-account', [ProfileManagerController::class, 'deleteAccountmanager']);

   // Home Routes
    Route::get('/inprogress-orders', [OrdersNotFinishedController::class, 'inProgressOrders']);
    Route::get('/new-orders', [OrdersNotFinishedController::class, 'NewOrders']);
    Route::get('/show-new-order/{id}', [OrdersNotFinishedController::class, 'ShowNewOrder']);
    Route::get('/stats', [OrdersNotFinishedController::class, 'stats']);

    // Order Routes
    Route::get('/completed-orders', [OrdersCompletedController::class, 'completedOrders']);
    Route::get('/delivered-orders', [OrdersDeliveredController::class,'deliveredOrders']);
    Route::get('/show-order/{id}', [OrdersCompletedController::class, 'show']);
    Route::get('/chef-reject-orders', [RejectedOrdersController::class, 'chefRejectedOrders']);
    Route::get('/delivery-reject-orders', [RejectedOrdersController::class, 'deliveryRejectedOrders']);
    Route::get('/returned-orders', [ReturnOrdersController::class, 'returnRequests']);

    // Order Maniuplation Routes
    Route::post('/accept-order/{id}', [ManagerController::class, 'acceptOrder']);
    Route::post('/assign-to-Chef', [ManagerController::class, 'assignToChef']);
    Route::post('/assign-order-delivery', [DeliveriesController::class, 'assignOrderToDelivery']);

    // Employees Routes
    Route::get('/chefs', [ChefController::class, 'chefs']);
    Route::get('/chef/{id}',     [ChefController::class, 'showChef']);
    Route::get('/current-requests', [ChefController::class, 'CurrentRequests']);
    Route::get('/deliveries', [DeliveriesController::class, 'AllDeliveries']);
    Route::get('/show-delivery/{id}', [DeliveriesController::class, 'showDelivery']);
    Route::get('/current-requests-delivery', [ChefController::class, 'CurrentRequestsDelivery']);

    // Addition Requests Routes
    Route::get('/addition', [AdditionRequestsController::class, 'Addition']);
    Route::post('/accept-chef/{id}', [AdditionRequestsController::class, 'acceptChef']);
    Route::post('/reject-chef/{id}', [AdditionRequestsController::class, 'rejectChef']);
    Route::post('/accept-delivery/{id}', [AdditionRequestsController::class, 'acceptDelivery']);
    Route::post('/reject-delivery/{id}', [AdditionRequestsController::class, 'rejectDelivery']);
    
    // Tracking Routes
    Route::post('/store-tracking', [TrackingPositionController::class, 'store']);
    Route::get('/show-latest/{id}', [TrackingPositionController::class, 'latest']);
    


});