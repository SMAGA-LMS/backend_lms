<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest\AddNewCourseRequest;
use App\Http\Requests\CourseRequest\AssignNewTeacherRequest;
use App\Http\Resources\ClassroomResource\ClassroomResource;
use App\Http\Resources\CourseResource\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
// use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CourseController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    // LMS-77, LMS-119
    // // pakai query param /courses?userID=xxx
    public function index(Request $request)
    {
        $picCourseID = $request->query('userID');

        //get courses
        try {
            $coursesQuery = DB::table('courses')
                ->leftJoin('users', 'courses.user_id', '=', 'users.id')
                ->select(
                    'courses.id',
                    'courses.name',
                    'courses.grade',

                    'users.id as user_id',
                    'users.name as user_name',
                    'users.username as user_username',
                    'users.role as user_role',
                    'users.avatar as user_avatar'
                );

            if (!empty($picCourseID)) {
                $coursesQuery->where('courses.user_id', $picCourseID);
            }

            $courses = $coursesQuery->get();
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve courses.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of courses retrieved successfully.";
        if (empty($courses)) $message = "No courses found.";

        return $this->apiResponse->successResponse(
            message: $message,
            data: CourseResource::collection($courses),
            codeResponse: 200
        );

        //return collection of users as a resource
        // return new CourseResource(true, 'List Course', $courses);
    }

    // LMS-81
    public function show($id)
    {
        if (!is_numeric($id) || intval($id) != $id) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Course ID",
                errors: ['Invalid Course ID'],
                codeResponse: 400
            );
        }

        //find course by ID
        $course = DB::table('courses')
            ->leftJoin('users', 'courses.user_id', '=', 'users.id')
            ->select(
                'courses.id',
                'courses.name',
                'courses.grade',

                'users.id as user_id',
                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar'
            )
            ->where('courses.id', $id)
            ->first();

        $message = "Course data retrieved successfully.";
        if (empty($course)) $message = "Course not found.";

        if ($course == null) {
            return $this->apiResponse->errorResponse(
                message: $message,
                errors: ['Course not found'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: $message,
            data: new CourseResource($course),
            codeResponse: 200
        );

        // //return single post as a resource
        // if ($course == null) {
        //     return new CourseResource(false, 'Course not found', $course);
        // } else {
        //     return new CourseResource(true, 'Detail Course', $course);
        // }
    }

    // LMS-33 => move to [LMS-119]
    // CHANGE: move to index() method, use query param userID
    // public function courseTeacherList(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id'      => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //get users
    //     $course = DB::table('courses')->where('user_id', $request->user_id)->get();
    //     // $course = Course::where('user_id', $request->user_id);

    //     if ($course == "[]") {
    //         return new CourseResource(false, 'No Courses found', $course);
    //     } else {
    //         //return collection of users as a resource
    //         return new CourseResource(true, 'Courses with desired teacher', $course);
    //     }
    // }

    // LMS-12
    public function courseGradeList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grade'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get users
        $course = DB::table('courses')->where('grade', $request->grade)->get();
        // $course = Course::where('user_id', $request->user_id);

        if ($course == "[]") {
            return new CourseResource(false, 'No Courses found', $course);
        } else {
            //return collection of users as a resource
            return new CourseResource(true, 'Courses with desired grade', $course);
        }
    }

    // LMS-76
    public function store(AddNewCourseRequest $request)
    {
        // pindah ke AddNewCourseRequest\AddNewCourseRequest untuk validasi
        //define validation rules
        // $validator = Validator::make($request->all(), [
        //     'name'     => 'required',
        //     'user_id' => 'string',
        //     'grade' => 'required',
        // ]);

        //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        $validatedNewCourse = $request->validated();

        $teacher = $validatedNewCourse['userID'] ?? null;

        // hindari value null tapi dijadiin string, lebih baik tipe data NULL aja
        // if ($teacher == null) {
        //     $teacher == "null";
        // }

        //create course
        try {
            $course = Course::create([
                'user_id'     => $teacher,
                'name' => $validatedNewCourse['name'],
                'grade' => $validatedNewCourse['grade'],
            ]);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to create new course.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        //return response
        // return new CourseResource(true, 'New Student-Class added', $course);
        return $this->apiResponse->successResponse(
            message: "New course added.",
            data: new CourseResource($course),
            codeResponse: 201
        );
    }

    // LMS-84
    public function update(AssignNewTeacherRequest $request, $id)
    {
        // CHANGE: move to AssignNewTeacherRequest
        // //define validation rules
        // $validator = Validator::make($request->all(), [
        //     'user_id' => 'required',
        // ]);

        // //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        $validatedTeacher = $request->validated();

        // $course = Course::find($id);
        try {
            $course = Course::find($id);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Course not found.",
                errors: $th->getMessage(),
                codeResponse: 404
            );
        }

        // $course->update([
        //     'user_id' => $request->user_id,
        // ]);
        $userID = $validatedTeacher['userID'] ?? null;
        if ($userID != null) {
            $userID = (int)$userID;
        }

        try {
            // ini belum return data join ke table user, jadi return response nya masih table course aja
            // object user ada, tapi ke isi yang user.id aja, kalau user.name, dll pasti null value nya (karena belum di-join)
            $course->update([
                'user_id' => $userID,
            ]);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to assign new teacher.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        // //return response
        // return new CourseResource(true, 'New Teacher added', $course);
        return $this->apiResponse->successResponse(
            message: "Teacher updated.",
            data: new CourseResource($course),
            codeResponse: 200
        );
    }
}
