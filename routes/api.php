<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::delete('/', [AuthController::class, 'destroy']);
    });
});

Route::prefix('folder')->middleware('auth:api')->group(function () {
    Route::post('/', [FolderController::class, 'store']);
    Route::get('/', [FolderController::class, 'index']);
    Route::get('/{id}', [FolderController::class, 'show']);
    Route::put('/{id}', [FolderController::class, 'update']);
    Route::delete('/{id}', [FolderController::class, 'destroy']);
});

Route::prefix('file')->group(function () {

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [FileController::class, 'upload']);
        Route::get('/', [FileController::class, 'index']);
        Route::get('/{id}', [FileController::class, 'show']);
        Route::get('/link/{file_id}', [FileController::class, 'generateLink']);
        Route::put('/{id}/folder/{folder_id}', [FileController::class, 'addToFolder']);
        Route::put('/{id}/folder', [FileController::class, 'removeFromFolder']);
        Route::put('/visibility/{id}', [FileController::class, 'changeVisibility']);
        Route::delete('/{id}', [FileController::class, 'destroy']);
    });

    Route::get('/download/{file}', [FileController::class, 'download']);
});
