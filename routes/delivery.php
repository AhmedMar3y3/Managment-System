<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delivery\AuthController;
use App\Http\Controllers\Delivery\ProfileController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify', [AuthController::class, 'verify']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-reset-code', [AuthController::class, 'verifyResetCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/branches', [AuthController::class, 'branches']);


Route::middleware(['auth.delivery'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::delete('/delete-account', [ProfileController::class, 'deleteAccount']);
});