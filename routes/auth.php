<?php

use App\Http\Controllers\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/register', [authController::class, 'register']);
Route::post('/login', [authController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [authController::class, 'logout']);
    Route::delete('/delete', [authController::class, 'delete']);
});
