<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
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

    public function index()
    {
        //get class
        // $courses = ClassEnrollment::all();
        try {
            $enrolledCourses = DB::table('class_enrollments')
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
                ->get();
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "An error occurred while fetching class enrollments",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of class enrollment retrieved successfully.";
        if (empty($enrolledCourses)) $message = "No class enrollment found.";

        //return collection of users as a resource
        // return new ClassEnrollmentResource(true, 'List Data Course-Class', $courses);
        return $this->apiResponse->successResponse(
            message: $message,
            data: ClassEnrollmentResource::collection($enrolledCourses),
            codeResponse: 200
        );
    }

    public function show($id)
    {
        //find class by ID
        $classenr = ClassEnrollment::find($id);

        //return single post as a resource
        if ($classenr == null) {
            return new ClassEnrollmentResource(false, 'Class-Course not found', $classenr);
        } else {
            return new ClassEnrollmentResource(true, 'Detail Class-Course', $classenr);
        }
    }

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

    public function getClassesCourseID(Request $request)
    {
        $courses = DB::table('class_enrollments')->where('classroom_id', $request->classroom_id)->get();

        if ($courses == "[]") {
            return new ClassEnrollmentResource(false, 'No Classes found', $courses);
        } else {
            //return collection of classes as a resource
            return new ClassEnrollmentResource(true, 'List Courses in a Class', $courses);
        }
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'course_id'      => 'required',
            'classroom_id'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create class
        $classes = ClassEnrollment::create([
            'course_id'     => $request->course_id,
            'classroom_id' => $request->classroom_id,
        ]);

        //return response
        return new ClassEnrollmentResource(true, 'New Class-Course added', $classes);
    }
}
