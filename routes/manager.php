<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\AuthController;



Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth.manager')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

});
