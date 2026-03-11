<?php

use App\Http\Controllers\FolderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function(){
Route::post('/',[FolderController::class,'createFolder']);
Route::get('/all',[FolderController::class,'index']);
Route::get('/{id}',[FolderController::class,'getFolder']);
Route::put('/update/{id}',[FolderController::class,'edit']);
Route::delete('/delete/{id}',[FolderController::class,'delete']);
});