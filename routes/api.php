<?php

use App\Http\Controllers\Api\AcademicTermController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ClassPeriodController;
use App\Http\Controllers\Api\GradeClassroomController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StudentEnrollmentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//default api
// Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);
// Route::apiResource('/classes', App\Http\Controllers\Api\ClassroomController::class);
// Route::apiResource('/studentenrollment', App\Http\Controllers\Api\StudentEnrollmentController::class);
// Route::apiResource('/courses', App\HI alttp\Controllers\Api\CourseController::class);
// Route::apiResource('/classenrollment', App\Http\Controllers\Api\ClassEnrollmentController::class);

// authentication
// biar ga harus nulis /users setiap route end point nya, karena udah di-group dari parent nya (prefix) otomatis jadi /users/login, dst
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware(['auth:sanctum']);
    Route::get('/me', [AuthenticationController::class, 'authMe'])->middleware(['auth:sanctum']);
});
// Route::post('/users/login', [App\Http\Controllers\Api\UserController::class, 'login']);


//user
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'getUserList'])->middleware(['auth:sanctum']);
    Route::post('/', [UserController::class, 'addNewUser'])->middleware(['auth:sanctum']);
});

// role
Route::get('/roles', [RoleController::class, 'getRoleList'])->middleware(['auth:sanctum']);

// class periods
Route::prefix('class-periods')->group(function () {
    Route::get('/', [ClassPeriodController::class, 'getClassPeriodList'])->middleware(['auth:sanctum']);
    Route::post('/', [ClassPeriodController::class, 'addNewClassPeriod'])->middleware(['auth:sanctum']);

    Route::get('/{classPeriodCode}', [ClassPeriodController::class, 'show'])->middleware(['auth:sanctum']);

    // student enrollment
    Route::get('/{classPeriodCode}/people', [StudentEnrollmentController::class, 'studentList'])->middleware(['auth:sanctum']);
});

// academic terms
Route::get('/academic-terms', [AcademicTermController::class, 'getAcademicTermList'])->middleware(['auth:sanctum']);

// grade classroom
Route::get('/grade-classrooms', [GradeClassroomController::class, 'getGradeClassroomList'])->middleware(['auth:sanctum']);

//student enrollment
// Route::post('/studentenrollment/class', [StudentEnrollmentController::class, 'studentList']);
// Route::post('/studentenrollment/studentclass', [App\Http\Controllers\Api\StudentEnrollmentController::class, 'studentClassroom']);

//course
// Route::post('/courses/teacher', [App\Http\Controllers\Api\CourseController::class, 'courseTeacherList']);
// Route::post('/courses/grade', [App\Http\Controllers\Api\CourseController::class, 'courseGradeList']);

//class enrollment
// Route::post('/classenrollment/class', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getCoursesClassID']);
// Route::post('/classenrollment/classlist', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getCoursesClassIDList']);
// Route::post('/classenrollment/course', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getClassesCourseID']);
