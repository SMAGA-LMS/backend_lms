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
    private $apiResponse;
    private $classEnrollment;

    public function __construct(ApiResponseHelper $apiResponse, ClassEnrollment $classEnrollment)
    {
        $this->apiResponse = $apiResponse;
        $this->classEnrollment = $classEnrollment;
    }

    // LMS-15, LMS-99, LMS-94, LMS-121
    public function index(Request $request)
    {
        //get class
        // $courses = ClassEnrollment::all();

        $filterFields = ['user_id', 'classroom_id'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        try {
            $classEnrollments = $this->classEnrollment->getClassEnrollmentsByCondition($filters);
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
        $classEnrollment = $this->classEnrollment->getClassEnrollmentByID($id);

        $message = "Class enrollments data retrieved successfully.";
        if (empty($classEnrollment)) $message = "Class enrollment not found.";

        //return single post as a resource
        // if ($classenr == null) {
        //     return new ClassEnrollmentResource(false, 'Class-Course not found', $classenr);
        // } else {
        //     return new ClassEnrollmentResource(true, 'Detail Class-Course', $classenr);
        // }
        if ($classEnrollment == null) {
            return $this->apiResponse->errorResponse(
                message: "Class enrollment not found.",
                errors: ['Class enrollment not found.'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: $message,
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
            $data = [
                'course_id'     => $validatedRequest['course_id'],
                'classroom_id' => $validatedRequest['classroom_id'],
                'user_id'       => $validatedRequest['user_id'],
            ];
            $newClassEnrollmentID = $this->classEnrollment->insertNewClassEnrollment($data);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "An error occurred while creating class enrollment",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $newClassEnrollment = $this->classEnrollment->getClassEnrollmentByID($newClassEnrollmentID);

        //return response
        // return new ClassEnrollmentResource(true, 'New Class-Course added', $classes);
        return $this->apiResponse->successResponse(
            message: "New class enrollment added",
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

        $validatedRequest = $request->validated();

        try {
            $classEnrollment = $this->classEnrollment->getClassEnrollmentByID($id);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve class enrollment.",
                errors: $th->getMessage(),
                codeResponse: 404
            );
        }

        if ($classEnrollment == null) {
            return $this->apiResponse->errorResponse(
                message: "Class enrollment not found.",
                errors: ['Class enrollment not found'],
                codeResponse: 404
            );
        }

        try {
            $data = [
                'user_id' => $validatedRequest['user_id'],
            ];
            $this->classEnrollment->updateClassEnrollment($id, $data);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to assign new teacher.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $updatedClassEnrollment = $this->classEnrollment->getClassEnrollmentByID($id);

        return $this->apiResponse->successResponse(
            message: "Teacher updated.",
            data: new ClassEnrollmentResource($updatedClassEnrollment),
            codeResponse: 200
        );
    }

    // LMS-132
    public function getStudentClassEnrollment($userID)
    {
        if (!is_numeric($userID) || intval($userID) != $userID) {
            return $this->apiResponse->errorResponse(
                message: "Invalid User ID",
                errors: ['Invalid User ID'],
                codeResponse: 400
            );
        }

        try {
            $classEnrollments = $this->classEnrollment->getStudentClassEnrollment($userID);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "An error occurred while fetching class enrollments for the student",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of class enrollment for student with ID: " . $userID . " retrieved successfully.";
        if (empty($classEnrollments)) $message = "No class enrollment found.";

        return $this->apiResponse->successResponse(
            message: $message,
            data: ClassEnrollmentResource::collection($classEnrollments),
            codeResponse: 200
        );
    }
}
