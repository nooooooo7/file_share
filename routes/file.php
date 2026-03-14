<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/', [FileController::class, 'upload']);
    Route::get('/', [FileController::class, 'index']);
    Route::get('/{id}', [FileController::class, 'show']);
    Route::get('/link/{file_id}', [FileController::class, 'generateLink']);
    Route::put('/{id}/folder/{folder_id}', [FileController::class, 'addToFolder']);
    Route::put('/visibility/{id}', [FileController::class, 'changeVisibility']);
    Route::delete('/delete/{id}', [FileController::class, 'delete']);
});

Route::get('/download/{file}', [FileController::class, 'download']);
