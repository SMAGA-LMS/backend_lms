<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/', function () {
    return view('hello');
});

// Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);
