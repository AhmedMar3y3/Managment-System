<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\OrdersDetailsController;
use App\Http\Controllers\Manager\DeliveriesController;
use App\Http\Controllers\Manager\ChefController;



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

    Route::post('order', [ManagerController::class, 'index']);
    Route::post('asignToChef', [ManagerController::class, 'assignToChef']);
    Route::post('acceptOrder/{id}', [ManagerController::class, 'acceptOrder']);
    Route:: post('/branches', [ManagerController::class, 'createBranch']);
    Route::post('logout', [AuthController::class, 'logout']);
    //OrderDetail_________________________________________________________________________________________________
    Route::get('completedOrders', [OrdersDetailsController::class, 'completedOrders']);
    Route::get('show-omplete-dOrders/{id}', [OrdersDetailsController::class, 'show']);
    //Deliveries/Detail_________________________________________________________________________________________________
    Route::get('AllDeliveries', [DeliveriesController::class, 'AllDeliveries']);
    Route::get('showDelivery/{id}', [DeliveriesController::class, 'showDelivery']);
    //AllchefsInformation_______________________________________________________________________________________________
    
    Route::get('AllchefsInformation', [ChefController::class, 'AllchefsInformation']);
    Route::get('chefDetail/{id}',     [ChefController::class, 'chefDetail']);

});
//____________________________________________________________________________________________________________
