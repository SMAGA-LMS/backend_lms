<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClassEnrollmentRequest\AddNewClassEnrollmentRequest;
use App\Http\Requests\ClassEnrollmentRequest\AssignNewTeacherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ClassEnrollment;
use App\Http\Resources\ClassEnrollmentResource\ClassEnrollmentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ClassEnrollmentController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    // LMS-15, LMS-99, LMS-94, LMS-121
    public function index(Request $request)
    {
        //get class
        // $courses = ClassEnrollment::all();

        $teacherID = $request->query('user_id');
        $classroomID = $request->query('classroom_id');
        try {
            $classEnrollmentsQuery = DB::table('class_enrollments')
                ->leftJoin('classrooms', 'class_enrollments.classroom_id', '=', 'classrooms.id')
                ->leftJoin('courses', 'class_enrollments.course_id', '=', 'courses.id')
                ->leftJoin('users', 'class_enrollments.user_id', '=', 'users.id')
                ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
                ->select(
                    'class_enrollments.id as id',
                    'class_enrollments.course_id',
                    'class_enrollments.classroom_id',

                    'classrooms.id as classroom_id',
                    'classrooms.name as classroom_name',
                    'classrooms.grade as classroom_grade',

                    'courses.id as course_id',
                    'courses.name as course_name',
                    'courses.user_id as course_user_id',
                    'courses.grade as course_grade',

                    'users.id as user_id',
                    'users.name as user_name',
                    'users.username as user_username',
                    'users.role as user_role',
                    'users.avatar as user_avatar',

                    'pic_courses.id as pic_course_id',
                    'pic_courses.name as pic_course_name',
                    'pic_courses.username as pic_course_username',
                    'pic_courses.role as pic_course_role',
                    'pic_courses.avatar as pic_course_avatar',
                );

            // get class enrollments by teacher ID
            if (!empty($teacherID)) {
                $classEnrollmentsQuery->where('class_enrollments.user_id', $teacherID);
            }

            if (!empty($classroomID)) {
                $classEnrollmentsQuery->where('class_enrollments.classroom_id', $classroomID);
            }

            $classEnrollments = $classEnrollmentsQuery->get();
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "An error occurred while fetching class enrollments",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of class enrollment retrieved successfully.";
        if (empty($classEnrollments)) $message = "No class enrollment found.";

        //return collection of users as a resource
        // return new ClassEnrollmentResource(true, 'List Data Course-Class', $courses);
        return $this->apiResponse->successResponse(
            message: $message,
            data: ClassEnrollmentResource::collection($classEnrollments),
            codeResponse: 200
        );
    }

    // LMS-17, LMS-101
    public function show($id)
    {
        if (!is_numeric($id) || intval($id) != $id) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Class Enrollment ID",
                errors: ['Invalid Class Enrollment ID'],
                codeResponse: 400
            );
        }

        //find class enrollment by ID
        // $classenr = ClassEnrollment::find($id);
        $classEnrollment = DB::table('class_enrollments')
            ->leftJoin('classrooms', 'class_enrollments.classroom_id', '=', 'classrooms.id')
            ->leftJoin('courses', 'class_enrollments.course_id', '=', 'courses.id')
            ->leftJoin('users', 'class_enrollments.user_id', '=', 'users.id')
            ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            ->select(
                'class_enrollments.id as id',
                'class_enrollments.course_id',
                'class_enrollments.classroom_id',
                'classrooms.id as classroom_id',
                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade',

                'courses.id as course_id',
                'courses.name as course_name',
                'courses.user_id as course_user_id',
                'courses.grade as course_grade',

                'users.id as user_id',
                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar',

                'pic_courses.id as pic_course_id',
                'pic_courses.name as pic_course_name',
                'pic_courses.username as pic_course_username',
                'pic_courses.role as pic_course_role',
                'pic_courses.avatar as pic_course_avatar',
            )
            ->where('class_enrollments.id', $id)
            ->first();

        $message = "Class enrollments data retrieved successfully.";
        if (empty($classEnrollment)) $message = "Class enrollment not found.";

        //return single post as a resource
        // if ($classenr == null) {
        //     return new ClassEnrollmentResource(false, 'Class-Course not found', $classenr);
        // } else {
        //     return new ClassEnrollmentResource(true, 'Detail Class-Course', $classenr);
        // }
        return $this->apiResponse->successResponse(
            message: "Class enrollment data retrieved successfully.",
            data: new ClassEnrollmentResource($classEnrollment),
            codeResponse: 200
        );
    }

    // LMS-16
    public function getCoursesClassID(Request $request)
    {
        $classes = DB::table('class_enrollments')->where('course_id', $request->course_id)->get()->first();

        if ($classes == null) {
            return new ClassEnrollmentResource(false, 'No Course or Classes found', $classes);
        } else {
            //return collection of classes as a resource
            return new ClassEnrollmentResource(true, 'List Classes in Course', $classes);
        }
    }

    // LMS-16
    public function getCoursesClassIDList(Request $request)
    {
        $classes = DB::table('class_enrollments')->where('course_id', $request->course_id)->get();

        if ($classes == "[]") {
            return new ClassEnrollmentResource(false, 'No Course or Classes found', $classes);
        } else {
            //return collection of classes as a resource
            return new ClassEnrollmentResource(true, 'List Classes in Course', $classes);
        }
    }

    // LMS-20 => move to [LMS-121]
    // CHANGE: move to index() method, use query param classroomID
    // public function getClassesCourseID(Request $request)
    // {
    //     $courses = DB::table('class_enrollments')->where('classroom_id', $request->classroom_id)->get();

    //     if ($courses == "[]") {
    //         return new ClassEnrollmentResource(false, 'No Classes found', $courses);
    //     } else {
    //         //return collection of classes as a resource
    //         return new ClassEnrollmentResource(true, 'List Courses in a Class', $courses);
    //     }
    // }

    // LMS-14, LMS-97
    public function store(AddNewClassEnrollmentRequest $request)
    {
        // CHANGE: move validation to AddNewClassEnrollmentRequest
        // //define validation rules
        // $validator = Validator::make($request->all(), [
        //     'course_id'      => 'required',
        //     'classroom_id'     => 'required',
        // ]);

        // //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        $validatedRequest = $request->validated();

        try {
            //create class
            // ini belum return data join ke table user, jadi return response nya masih table class enrollment aja
            // object user ada, tapi ke isi yang user.id aja, kalau user.name, dll pasti null value nya (karena belum di-join)
            $newClassEnrollment = ClassEnrollment::create([
                'course_id'     => $validatedRequest['course_id'],
                'classroom_id' => $validatedRequest['classroom_id'],
                'user_id'       => $validatedRequest['user_id'],
            ]);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "An error occurred while creating class enrollment",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        //return response
        // return new ClassEnrollmentResource(true, 'New Class-Course added', $classes);
        return $this->apiResponse->successResponse(
            message: "New enrolled course added",
            data: new ClassEnrollmentResource($newClassEnrollment),
            codeResponse: 201
        );
    }

    // LMS-103
    public function update(AssignNewTeacherRequest $request, $id)
    {
        if (!is_numeric($id) || intval($id) != $id) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Class Enrollment ID",
                errors: ['Invalid Class Enrollment ID'],
                codeResponse: 400
            );
        }

        $validatedTeacher = $request->validated();

        try {
            $classEnrollment = ClassEnrollment::find($id);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Class Enrollment not found.",
                errors: $th->getMessage(),
                codeResponse: 404
            );
        }

        $userID = $validatedTeacher['user_id'];

        try {
            $classEnrollment->update([
                'user_id' => $userID,
            ]);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to assign new teacher.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        return $this->apiResponse->successResponse(
            message: "Teacher updated.",
            data: new ClassEnrollmentResource($classEnrollment),
            codeResponse: 200
        );
    }
}
