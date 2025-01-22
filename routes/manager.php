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
//_____________________________________________________________________________________________________________



// Authentication Routes______________________________________________________________________________________
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('status', [ManagerController::class, 'status']);

// Password Reset Routes______________________________________________________________________________________
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-code', [AuthController::class, 'verifyCode']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Verification Routes________________________________________________________________________________________
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('switch/{id}', [ManagerController::class, 'switchStatusToApproved']);

// Protected Routes___________________________________________________________________________________________
    Route::middleware('auth.manager')->group(function () {
//____________________________________________________________________________________________________________
    Route::post('/assign-to-Chef', [ManagerController::class, 'assignToChef']);
    Route::post('/accept-order/{id}', [ManagerController::class, 'acceptOrder']);
    Route::post('/logout', [AuthController::class, 'logout']);
//OrderDetail________________________________________________________________________________________________
    Route::get('/completed-orders', [OrdersCompletedController::class, 'completedOrders']);
    Route::get('/show-order/{id}', [OrdersCompletedController::class, 'show']);
    //Deliveries/Detail__________________________________________________________________________________________
    Route::get('/deliveries', [DeliveriesController::class, 'AllDeliveries']);
    Route::get('/show-delivery/{id}', [DeliveriesController::class, 'showDelivery']);
    Route::post('/assign-order-delivery', [DeliveriesController::class, 'assignOrderToDelivery']);

//AllchefsInformation________________________________________________________________________________________
    Route::get('/chefs', [ChefController::class, 'chefs']);
    Route::get('/chef/{id}',     [ChefController::class, 'showChef']);
    Route::get('/current-requests', [ChefController::class, 'CurrentRequests']);
//OrdersNotFinshed_______________________________________________________________________________________________
    Route::get('/inprogress-orders', [OrdersNotFinishedController::class, 'inProgressOrders']);
    Route::get('/new-orders', [OrdersNotFinishedController::class, 'NewOrders']);
    Route::get('/show-new-order/{id}', [OrdersNotFinishedController::class, 'ShowNewOrder']);
    Route::get('/stats', [OrdersNotFinishedController::class, 'stats']);
    
    //OrdersDelivered______________________________________________________________________________________________
    Route::get('/delivered-orders', [OrdersDeliveredController::class,'deliveredOrders']);
    ///
    //additionrerquests_____________________________________________________________________________________________
    Route::get('/addition', [AdditionRequestsController::class, 'Addition']);
    Route::post('/accept-chef', [AdditionRequestsController::class, 'acceptChef']);
    Route::post('/reject-chef', [AdditionRequestsController::class, 'rejectChef']);
    Route::post('/accept-delivery', [AdditionRequestsController::class, 'acceptDelivery']);
    Route::post('/reject-delivery', [AdditionRequestsController::class, 'rejectDelivery']);
    
    //MANAGE/Rprofile/___________________________________________________________________________________________________________________-
    Route::get('/profile', [ProfileManagerController::class, 'getProfilemanager']);
    Route::put('/profile', [ProfileManagerController::class, 'updateProfilemanager']);
    Route::delete('/delete-account', [ProfileManagerController::class, 'deleteAccountmanager']);
    // tracking_position_______________________________________________________________________________________________________________
    Route::post('/store-tracking', [TrackingPositionController::class, 'store']);
    Route::get('/show-latest/{id}', [TrackingPositionController::class, 'latest']);
//________________________________________________________________________________________________________________________

});