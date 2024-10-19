<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/*
    behind this route apiResource laravel
    Route::apiResource('/courses', CourseController::class);

    it will create these end point
    GET /courses → CourseController@index
    GET /courses/{course} → CourseController@show
    POST /courses → CourseController@store
    PUT/PATCH /courses/{course} → CourseController@update
    DELETE /courses/{course} → CourseController@destroy
*/

//default api
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('/users', UserController::class);
    // CHANGE: better penamaan end point samain, di sini penamaannya classroom,
    // jadi end point nya classrooms aja
    Route::apiResource('/classrooms', ClassroomController::class);
    Route::apiResource('/courses', CourseController::class);
});

// dipindahin ke atas, pake middleware auth:sanctum
// Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);
// Route::apiResource('/classes', App\Http\Controllers\Api\ClassroomController::class);
// Route::apiResource('/studentenrollment', App\Http\Controllers\Api\StudentEnrollmentController::class);
// Route::apiResource('/courses', App\Http\Controllers\Api\CourseController::class);
// Route::apiResource('/classenrollment', App\Http\Controllers\Api\ClassEnrollmentController::class);
// Route::apiResource('/modules', App\Http\Controllers\Api\ModuleController::class);
// Route::apiResource('/readmodules', App\Http\Controllers\Api\ReadModuleController::class);
// Route::apiResource('/requirements', App\Http\Controllers\Api\RequirementController::class);

//user
// CHANGE: refactor menggunakan prefix (mirip di-grouping)
// reason: biar ga perlu nulis /users/login, /users/..., cukup /login
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware(['auth:sanctum']);
    Route::get('/me', [AuthenticationController::class, 'authMe'])->middleware(['auth:sanctum']);
});

// Route::post('/users/login', [App\Http\Controllers\Api\UserController::class, 'login']);

// CHANGE: ubah jadi menggunakan query param (/users?role=RoleName)
// jangan dipaksain pakai POST juga
// karena udah pakai template apiResource, jadi kerjain nya di UserController method show aja

// Route::post('/users/admins', [App\Http\Controllers\Api\UserController::class, 'adminList']);
// Route::post('/users/students', [App\Http\Controllers\Api\UserController::class, 'studentList']);
// Route::post('/users/teachers', [App\Http\Controllers\Api\UserController::class, 'teacherList']);



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
