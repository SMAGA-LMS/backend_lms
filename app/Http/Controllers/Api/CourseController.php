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
    private $apiResponse;
    private $course;

    public function __construct(ApiResponseHelper $apiResponse, Course $course)
    {
        $this->apiResponse = $apiResponse;
        $this->course = $course;
    }

    // LMS-77, LMS-119
    // // pakai query param /courses?user_id=xxx
    public function index(Request $request)
    {
        $filterFields = ['user_id'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        //get courses
        try {
            $courses = $this->course->getCoursesByCondition($filters);
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
        try {
            $course = $this->course->getCourseByID($id);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve course.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

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

        $teacherID = $validatedNewCourse['user_id'] ?? null;

        // hindari value null tapi dijadiin string, lebih baik tipe data NULL aja
        // if ($teacher == null) {
        //     $teacher == "null";
        // }

        //create course
        try {
            $data = [
                'user_id'     => $teacherID,
                'name' => $validatedNewCourse['name'],
                'grade' => $validatedNewCourse['grade'],
            ];
            $newCourseID = $this->course->insertNewCourse($data);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to create new course.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $newCourse = $this->course->getCourseByID($newCourseID);

        //return response
        // return new CourseResource(true, 'New Student-Class added', $course);
        return $this->apiResponse->successResponse(
            message: "New course added.",
            data: new CourseResource($newCourse),
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

        $validatedRequest = $request->validated();

        // $course = Course::find($id);
        try {
            $course = $this->course->getCourseByID($id);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve course.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        if ($course == null) {
            return $this->apiResponse->errorResponse(
                message: "Course not found.",
                errors: ['Course not found'],
                codeResponse: 404
            );
        }

        // $course->update([
        //     'user_id' => $request->user_id,
        // ]);
        try {
            // ini belum return data join ke table user, jadi return response nya masih table course aja
            // object user ada, tapi ke isi yang user.id aja, kalau user.name, dll pasti null value nya (karena belum di-join)
            $data = [
                'user_id' => $validatedRequest['user_id'],
            ];
            $this->course->updateCourse($id, $data);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to assign new teacher.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $updatedCourse = $this->course->getCourseByID($id);

        // //return response
        // return new CourseResource(true, 'New Teacher added', $course);
        return $this->apiResponse->successResponse(
            message: "Teacher updated.",
            data: new CourseResource($updatedCourse),
            codeResponse: 200
        );
    }
}
