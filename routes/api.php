<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\BranchController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\ChefController;
use App\Http\Controllers\Dashboard\DeliveryController;
use App\Http\Controllers\Dashboard\FlowerController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\ManagerController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\SalesController;
use App\Http\Controllers\Dashboard\SpecializationController;

//////////////////////////////////////////Admin routes//////////////////////////////////////////

    //Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth.admin'])->group(function () {
        
    // Home routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/stats', [HomeController::class,'stats']);

    //Branch routes
    Route::get('/branches', [BranchController::class, 'index']);
    Route::post('/branches', [BranchController::class, 'store']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::put('/branches/{id}', [BranchController::class, 'update']);
    Route::delete('/branches/{id}', [BranchController::class, 'destroy']);

    // Banner routes
    Route::get('/banners', [BannerController::class,'index']);
    Route::post('/banner', [BannerController::class,'store']);
    Route::delete('/banner/{id}', [BannerController::class,'destroy']);
    Route::get('/banner/{id}', [BannerController::class,'show']);

    // Manager routes
    Route::get('/managers', [ManagerController::class,'index']);
    Route::get('/manager/{id}', [ManagerController::class,'show']);
    Route::get('/pending-managers', [ManagerController::class,'pendingManagers']);
    Route::post('/accept-manager/{id}', [ManagerController::class,'acceptManager']);
    Route::post('/reject-manager/{id}', [ManagerController::class,'rejectManager']);
    Route::delete('/delete-manager/{id}', [ManagerController::class,'deleteManager']);

    // Sales routes
    Route::get('/sales', [SalesController::class,'index']);
    Route::get('/sale/{id}', [SalesController::class,'show']);
    Route::get('/pending-sales', [SalesController::class,'pendingSales']);
    Route::post('/accept-sale/{id}', [SalesController::class,'acceptSale']);
    Route::post('/reject-sale/{id}', [SalesController::class,'rejectSale']);
    Route::delete('/delete-sale/{id}', [SalesController::class,'deleteSale']);

    // Chef routes
    Route::get('/chefs', [ChefController::class,'index']);
    Route::get('/chef/{id}', [ChefController::class,'show']);
    Route::delete('/delete-chef/{id}', [ChefController::class,'delete']);

    // Delivery routes
    Route::get('/deliveries', [DeliveryController::class,'index']);
    Route::get('/delivery/{id}', [DeliveryController::class,'show']);
    Route::delete('/delete-delivery/{id}', [DeliveryController::class,'delete']);

    // Order routes
    Route::get('/orders', [OrderController::class,'index']);
    Route::get('/order/{id}', [OrderController::class,'show']);

    // Flower routes
    Route::get('/flowers', [FlowerController::class,'index']);
    Route::post('/store-flower', [FlowerController::class,'store']);
    Route::delete('/flower/{id}', [FlowerController::class,'destroy']);

    // Product routes
    Route::get('/products', [ProductController::class,'index']);
    Route::get('/product/{id}', [ProductController::class,'show']);
    Route::post('/store-product', [ProductController::class,'store']);
    Route::post('/update-product/{id}', [ProductController::class,'update']);
    Route::delete('/product/{id}', [ProductController::class,'destroy']);

    // Specialization routes
    Route::get('/specializations', [SpecializationController::class,'index']);
    Route::post('/specialization', [SpecializationController::class,'store']);
    Route::delete('/specialization/{id}', [SpecializationController::class,'destroy']);
});


