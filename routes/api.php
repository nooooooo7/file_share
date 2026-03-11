<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function(){
    require 'auth.php';
});

Route::prefix('folder')->group(function(){
    require 'folder.php';
});
Route::prefix('file')->group(function(){
    require 'file.php';
});