<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\BranchController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\ManagerController;
use App\Http\Controllers\Dashboard\SalesController;

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
});


