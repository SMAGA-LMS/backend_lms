<?php

use App\Http\Controllers\Api\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//default api
Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);
Route::apiResource('/classes', App\Http\Controllers\Api\ClassroomController::class);
Route::apiResource('/studentenrollment', App\Http\Controllers\Api\StudentEnrollmentController::class);
Route::apiResource('/courses', App\Http\Controllers\Api\CourseController::class);
Route::apiResource('/classenrollment', App\Http\Controllers\Api\ClassEnrollmentController::class);
Route::apiResource('/modules', App\Http\Controllers\Api\ModuleController::class);
Route::apiResource('/readmodules', App\Http\Controllers\Api\ReadModuleController::class);
Route::apiResource('/requirements', App\Http\Controllers\Api\RequirementController::class);

//user
// CHANGE: refactor menggunakan prefix (mirip di-grouping)
// reason: biar ga perlu nulis /users/login, /users/..., cukup /login
Route::prefix('users')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
});

// Route::post('/users/login', [App\Http\Controllers\Api\UserController::class, 'login']);

Route::post('/users/admins', [App\Http\Controllers\Api\UserController::class, 'adminList']);
Route::post('/users/students', [App\Http\Controllers\Api\UserController::class, 'studentList']);
Route::post('/users/teachers', [App\Http\Controllers\Api\UserController::class, 'teacherList']);

//student enrollment
Route::post('/studentenrollment/class', [App\Http\Controllers\Api\StudentEnrollmentController::class, 'studentList']);
Route::post('/studentenrollment/studentclass', [App\Http\Controllers\Api\StudentEnrollmentController::class, 'studentClassroom']);

//course
Route::post('/courses/teacher', [App\Http\Controllers\Api\CourseController::class, 'courseTeacherList']);
Route::post('/courses/grade', [App\Http\Controllers\Api\CourseController::class, 'courseGradeList']);

//class enrollment
Route::post('/classenrollment/class', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getCoursesClassID']);
Route::post('/classenrollment/classlist', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getCoursesClassIDList']);
Route::post('/classenrollment/course', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getClassesCourseID']);

//module

//readmodule
Route::post('/readmodules/find', [App\Http\Controllers\Api\ReadModuleController::class, 'find']);
