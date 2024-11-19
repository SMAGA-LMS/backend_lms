<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentEnrollmentRequest\AssignNewStudentRequest;
use App\Http\Resources\StudentEnrollmentResource\StudentEnrollmentResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentEnrollment;
use App\Models\User;

class StudentEnrollmentController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function index()
    {
        // //get class
        // $classes = StudentEnrollment::all();

        try {
            $studentEnrollments = StudentEnrollment::query()
                ->join('users', 'student_enrollments.user_id', '=', 'users.id')
                ->join('classrooms', 'student_enrollments.classroom_id', '=', 'classrooms.id')
                ->select(
                    'student_enrollments.id as id',
                    'student_enrollments.classroom_id',
                    'student_enrollments.user_id',
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.username as user_username',
                    'users.role as user_role',
                    'users.avatar as user_avatar',
                    'classrooms.id as classroom_id',
                    'classrooms.name as classroom_name',
                    'classrooms.grade as classroom_grade'
                )
                ->get();
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: 'An error occurred while fetching student enrollments',
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of users retrieved successfully.";
        if (empty($studentEnrollments)) $message = "No users found.";

        // //return collection of users as a resource
        // return new StudentEnrollmentResource(true, 'List Data Student-Class', $classes);
        return $this->apiResponse->successResponse(
            message: $message,
            data: StudentEnrollmentResource::collection($studentEnrollments),
            codeResponse: 200
        );
    }

    public function show($id)
    {
        return null;
    }

    public function store(AssignNewStudentRequest $request)
    {
        $validatedRequest = $request->validated();

        // CHANGE: move to AssignNewStudentRequest
        // //define validation rules
        // $validator = Validator::make($request->all(), [
        //     'user_id'      => 'required',
        //     'classroom_id'     => 'required',
        // ]);

        // //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        //create student enrollment
        try {
            // Check if the student is already enrolled in the classroom
            $existingEnrollment = StudentEnrollment::where('user_id', $validatedRequest['userID'])
                ->where('classroom_id', $validatedRequest['classroomID'])
                ->first();

            if ($existingEnrollment) {
                return $this->apiResponse->errorResponse(
                    message: 'The student is already enrolled in this classroom.',
                    errors: ['Student already enrolled'],
                    codeResponse: 409
                );
            }

            // Create new student enrollment
            $newStudentEnrollment = StudentEnrollment::create([
                'user_id'     => $validatedRequest['userID'],
                'classroom_id' => $validatedRequest['classroomID'],
            ]);

            $studentEnrollmentData = StudentEnrollment::query()
                ->join('users', 'student_enrollments.user_id', '=', 'users.id')
                ->join('classrooms', 'student_enrollments.classroom_id', '=', 'classrooms.id')
                ->select(
                    'student_enrollments.id as id',
                    'student_enrollments.classroom_id',
                    'student_enrollments.user_id',
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.username as user_username',
                    'users.role as user_role',
                    'users.avatar as user_avatar',
                    'classrooms.id as classroom_id',
                    'classrooms.name as classroom_name',
                    'classrooms.grade as classroom_grade'
                )
                ->where('student_enrollments.id', $newStudentEnrollment->id)
                ->first();
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: 'An error occurred while assigning a new student',
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        //return response
        // return new StudentEnrollmentResource(true, 'New Student-Class added', $classes);
        return $this->apiResponse->successResponse(
            message: 'New student added to the class.',
            data: new StudentEnrollmentResource($studentEnrollmentData),
            codeResponse: 201
        );
    }

    // CHANGE: move to getStudentsByClassroom()
    // public function studentList(Request $request)
    // {
    //     //get users
    //     $users = DB::table('student_enrollments')->where('classroom_id', $request->classroom_id)->get();

    //     if ($users == "[]") {
    //         return new StudentEnrollmentResource(false, 'No Students found', $users);
    //     } else {
    //         //return collection of users as a resource
    //         return new StudentEnrollmentResource(true, 'List Student in Class', $users);
    //     }
    // }

    // note: student terdaftar di kelas mana aja
    public function studentClassroom(Request $request)
    {
        //get users
        // $users = DB::table('student_enrollments')->where('user_id', $request->user_id)->get();
        $users = StudentEnrollment::where('user_id', $request->user_id)->first();

        if ($users == "[]") {
            return new StudentEnrollmentResource(false, 'No Students found', $users);
        } else {
            //return collection of users as a resource
            return new StudentEnrollmentResource(true, 'Students Class', $users->classroom_id);
        }
    }

    public function getStudentsByClassroom(Request $request, $id)
    {
        if (!is_numeric($id) || intval($id) != $id) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Classroom ID.",
                errors: ['Invalid Classroom ID'],
                codeResponse: 400
            );
        }

        $classroom = DB::table('classrooms')->where('id', intval($id))->first();
        if ($classroom == null) {
            return $this->apiResponse->errorResponse(
                message: "Classroom not found.",
                errors: ['Classroom not found'],
                codeResponse: 404
            );
        }

        $isAvailable = $request->query('isAvailable');
        if ($isAvailable == true) {
            return $this->getAvailableStudents($id);
        }

        $studentEnrollments = StudentEnrollment::where('classroom_id', $id)
            ->join('users', 'student_enrollments.user_id', '=', 'users.id')
            ->join('classrooms', 'student_enrollments.classroom_id', '=', 'classrooms.id')
            ->select(
                'student_enrollments.id as id',
                'student_enrollments.classroom_id',
                'student_enrollments.user_id',
                'users.id as user_id',
                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar',
                'classrooms.id as classroom_id',
                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade'
            )
            ->get()
            ->toArray();

        if (empty($studentEnrollments)) {
            return $this->apiResponse->successResponse(
                message: "No student found in classroom " . $classroom->name,
                data: [],
                codeResponse: 200
            );
        }

        return $this->apiResponse->successResponse(
            message: "List of students retrieved successfully.",
            data: StudentEnrollmentResource::collection($studentEnrollments),
            codeResponse: 200
        );
    }

    // note: dapetin list student yang belum terdaftar di kelas tersebut (classroomID), biar ga duplicate student yang sama di kelas yang sama
    public function getAvailableStudents($classroomID)
    {
        // $validatedRequest = $request->validated();
        // $classroomID = $validatedRequest['classroomID'];

        if (!is_numeric($classroomID) || intval($classroomID) != $classroomID) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Classroom ID.",
                errors: ['Invalid Classroom ID'],
                codeResponse: 400
            );
        }

        $listOfEnrolledUsersID = StudentEnrollment::where('classroom_id', $classroomID)->pluck('user_id');

        $availableUsers = User::whereNotIn('id', $listOfEnrolledUsersID)->get();

        $message = "List of available users retrieved successfully.";
        if ($availableUsers->isEmpty()) $message = "No available user found.";

        return $this->apiResponse->successResponse(
            message: $message,
            data: UserResource::collection($availableUsers),
            codeResponse: 200
        );
    }
}
