<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\ManagerController;



// Authentication Routes______________________________________________________________________________________
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Password Reset Routes______________________________________________________________________________________
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-code', [AuthController::class, 'verifyCode']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Verification Routes________________________________________________________________________________________
Route::post('verify', [AuthController::class, 'verify']);

// Protected Routes___________________________________________________________________________________________
Route::middleware('auth.manager')->group(function () {

    Route::post('order', [ManagerController::class, 'index']);
    Route::post('asignToChef', [ManagerController::class, 'asignToChef']);
    Route::post('acceptOrder/{id}', [ManagerController::class, 'acceptOrder']);
    Route:: post('/branches', [ManagerController::class, 'createBranch']);
    
    
    Route::post('logout', [AuthController::class, 'logout']);

});
//____________________________________________________________________________________________________________
