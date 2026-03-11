<?php

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->group(function () {
Route::post('/upload',[FileController::class,'upload']);
Route::get('/',[FileController::class,'index']);
Route::get('/link/{file_id}',[FileController::class,'generateLink']);
Route::get('/search',[FileController::class,'search']);
Route::put("/{id}/folder/{folder_id}",[FileController::class,'addToFolder']);
Route::put("/visibility/{id}",[FileController::class,'changeVisibility']);
Route::delete("/delete/{id}",[FileController::class,'delete']);
});

Route::get('/download/{file}', [FileController::class, 'download']);