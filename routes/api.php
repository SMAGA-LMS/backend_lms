<?php

use App\Http\Controllers\Api\AuthenticationController;
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
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
});


//user
// Route::post('/users/login', [App\Http\Controllers\Api\UserController::class, 'login']);
// Route::post('/users/admins', [App\Http\Controllers\Api\UserController::class, 'adminList']);
// Route::post('/users/students', [App\Http\Controllers\Api\UserController::class, 'studentList']);
// Route::post('/users/teachers', [App\Http\Controllers\Api\UserController::class, 'teacherList']);

//student enrollment
// Route::post('/studentenrollment/class', [App\Http\Controllers\Api\StudentEnrollmentController::class, 'studentList']);
// Route::post('/studentenrollment/studentclass', [App\Http\Controllers\Api\StudentEnrollmentController::class, 'studentClassroom']);

//course
// Route::post('/courses/teacher', [App\Http\Controllers\Api\CourseController::class, 'courseTeacherList']);
// Route::post('/courses/grade', [App\Http\Controllers\Api\CourseController::class, 'courseGradeList']);

//class enrollment
// Route::post('/classenrollment/class', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getCoursesClassID']);
// Route::post('/classenrollment/classlist', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getCoursesClassIDList']);
// Route::post('/classenrollment/course', [App\Http\Controllers\Api\ClassEnrollmentController::class, 'getClassesCourseID']);
