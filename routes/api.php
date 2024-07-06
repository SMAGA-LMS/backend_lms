<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);

Route::post('/users/login', [App\Http\Controllers\Api\UserController::class, 'login']);

Route::post('/users/admins', [App\Http\Controllers\Api\UserController::class, 'adminList']);

Route::post('/users/students', [App\Http\Controllers\Api\UserController::class, 'studentList']);

Route::post('/users/teachers', [App\Http\Controllers\Api\UserController::class, 'teacherList']);

