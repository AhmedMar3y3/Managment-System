<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\BranchController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\ChefController;
use App\Http\Controllers\Dashboard\DeliveryController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\ManagerController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\SalesController;
use App\Http\Controllers\Dashboard\SpecializationController;

// public routes for all users
Route::get('/all-specializations', [Controller::class, 'specializations']);
Route::get('/all-branches',        [Controller::class, 'branches']);

//////////////////////////////////////////Admin routes//////////////////////////////////////////

//Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::middleware(['auth.admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile routes
    Route::get('/profile',          [ProfileController::class, 'profile']);
    Route::put('/profile',          [ProfileController::class, 'updateProfile']);
    Route::post('/change-password', [ProfileController::class, 'changePassword']);

    // Home routes
    Route::get('/orders-stats', [HomeController::class, 'orders']);
    Route::get('/stats',        [HomeController::class, 'stats']);
    Route::get('/requests',     [HomeController::class, 'requests']);
    Route::get('/percentages',  [HomeController::class, 'percentages']);

    //Branch routes
    Route::get('/branches',         [BranchController::class, 'index']);
    Route::post('/branches',        [BranchController::class, 'store']);
    Route::get('/branches/{id}',    [BranchController::class, 'show']);
    Route::put('/branches/{id}',    [BranchController::class, 'update']);
    Route::delete('/branches/{id}', [BranchController::class, 'destroy']);

    // Banner routes
    Route::get('/banners',        [BannerController::class, 'index']);
    Route::post('/banner',        [BannerController::class, 'store']);
    Route::delete('/banner/{id}', [BannerController::class, 'destroy']);
    Route::get('/banner/{id}',    [BannerController::class, 'show']);

    // Manager routes
    Route::get('/managers',              [ManagerController::class, 'index']);
    Route::get('/manager/{id}',          [ManagerController::class, 'show']);
    Route::post('/accept-manager/{id}',  [ManagerController::class, 'acceptManager']);
    Route::post('/reject-manager/{id}',  [ManagerController::class, 'rejectManager']);
    Route::delete('/delete-manager/{id}',[ManagerController::class, 'deleteManager']);
    Route::get('/pending-managers',      [ManagerController::class, 'pendingManagers']);

    // Sales routes
    Route::get('/sales',               [SalesController::class, 'index']);
    Route::get('/sale/{id}',           [SalesController::class, 'show']);
    Route::post('/accept-sale/{id}',   [SalesController::class, 'acceptSale']);
    Route::post('/reject-sale/{id}',   [SalesController::class, 'rejectSale']);
    Route::delete('/delete-sale/{id}', [SalesController::class, 'deleteSale']);
    Route::get('/pending-sales',       [SalesController::class, 'pendingSales']);

    // Chef routes
    Route::get('/chefs',               [ChefController::class, 'index']);
    Route::get('/chef/{id}',           [ChefController::class, 'show']);
    Route::delete('/delete-chef/{id}', [ChefController::class, 'delete']);

    // Delivery routes
    Route::get('/deliveries',              [DeliveryController::class, 'index']);
    Route::get('/delivery/{id}',           [DeliveryController::class, 'show']);
    Route::delete('/delete-delivery/{id}', [DeliveryController::class, 'delete']);

    // Order routes
    Route::get('/orders',           [OrderController::class, 'index']);
    Route::get('/order/{id}',       [OrderController::class, 'show']);
    Route::get('/new-orders',       [OrderController::class, 'newOrders']);
    Route::get('/completed-orders', [OrderController::class, 'completedOrders']);
    Route::get('/delivered-orders', [OrderController::class, 'deliveredOrders']);
    Route::get('/rejected-orders',  [OrderController::class, 'rejectedOrders']);
    Route::get('/returned-orders',  [OrderController::class, 'returnedOrders']);
    Route::get('/pending-orders',   [OrderController::class,'pendingOrders']);

    // Specialization routes
    Route::get('/specializations',        [SpecializationController::class, 'index']);
    Route::post('/specialization',        [SpecializationController::class, 'store']);
    Route::delete('/specialization/{id}', [SpecializationController::class, 'destroy']);
});
