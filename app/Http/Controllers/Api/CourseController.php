<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
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

    //
    public function index()
    {
        //get class
        $courses = Course::with('user')->get();

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

    public function show($id)
    {
        //find post by ID
        $course = Course::find($id);

        //return single post as a resource
        if ($course == null) {
            return new CourseResource(false, 'Course not found', $course);
        } else {
            return new CourseResource(true, 'Detail Course', $course);
        }
    }

    public function courseTeacherList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get users
        $course = DB::table('courses')->where('user_id', $request->user_id)->get();
        // $course = Course::where('user_id', $request->user_id);

        if ($course == "[]") {
            return new CourseResource(false, 'No Courses found', $course);
        } else {
            //return collection of users as a resource
            return new CourseResource(true, 'Courses with desired teacher', $course);
        }
    }

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

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'user_id' => 'string',
            'grade' => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $teacher = $request->user_id;

        if ($teacher == null) {
            $teacher == "null";
        }

        //create class
        $course = Course::create([
            'user_id'     => $request->user_id,
            'name' => $request->name,
            'grade' => $request->grade,
        ]);

        //return response
        return new CourseResource(true, 'New Student-Class added', $course);
    }

    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course = Course::find($id);
        $course->update([
            'user_id' => $request->user_id,
        ]);

        //return response
        return new CourseResource(true, 'New Teacher added', $course);
    }
}
