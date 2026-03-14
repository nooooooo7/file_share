<?php

use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/', [FolderController::class, 'store']);
    Route::get('/all', [FolderController::class, 'index']);
    Route::get('/{id}', [FolderController::class, 'show']);
    Route::put('/update/{id}', [FolderController::class, 'update']);
    Route::delete('/delete/{id}', [FolderController::class, 'destroy']);
});
