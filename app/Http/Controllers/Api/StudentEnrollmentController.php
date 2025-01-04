<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentEnrollmentRequest\AssignNewStudentRequest;
use App\Http\Resources\StudentEnrollmentResource\AvailableStudentsResource;
use App\Http\Resources\StudentEnrollmentResource\StudentEnrollmentResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentEnrollment;
use App\Models\User;

class StudentEnrollmentController extends Controller
{
    private $apiResponse;
    private $studentEnrollment;
    private $user;

    public function __construct(ApiResponseHelper $apiResponse, StudentEnrollment $studentEnrollment, User $user)
    {
        $this->apiResponse = $apiResponse;
        $this->studentEnrollment = $studentEnrollment;
        $this->user = $user;
    }

    // LMS-89, LMS-92, LMS-122
    public function index(Request $request)
    {
        // //get class
        // $classes = StudentEnrollment::all();

        $filterFields = ['classroom_id', 'user_id'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        $isAvailable = $request->query('is_available');
        if ($isAvailable == true) {
            if (empty($filters['classroom_id'])) {
                return $this->apiResponse->errorResponse(
                    message: 'Classroom ID is required to get available students.',
                    errors: ['Classroom ID is required'],
                    codeResponse: 400
                );
            }
            return $this->getAvailableStudents($filters);
        }

        try {
            $studentEnrollments = $this->studentEnrollment->getStudentEnrollmentsByCondition($filters);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: 'An error occurred while fetching student enrollments',
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of student enrollments retrieved successfully.";
        if (empty($studentEnrollments)) $message = "No student enrollments found.";

        // //return collection of users as a resource
        // return new StudentEnrollmentResource(true, 'List Data Student-Class', $classes);

        // otherwise (classroomID not exists from query param), return StudentEnrollmentResource
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

    // LMS-87
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
            $existingEnrollment = $this->studentEnrollment->getEnrolledUser($validatedRequest['user_id'], $validatedRequest['classroom_id']);

            if ($existingEnrollment) {
                return $this->apiResponse->errorResponse(
                    message: 'The student is already enrolled in this classroom.',
                    errors: ['Student already enrolled'],
                    codeResponse: 409
                );
            }

            // Create new student enrollment
            // ini belum return data join ke table user, jadi return response nya masih table student_enrollment aja
            // object user ada, tapi ke isi yang user.id aja, kalau user.name, dll pasti null value nya (karena belum di-join)
            $data = [
                'user_id'     => $validatedRequest['user_id'],
                'classroom_id' => $validatedRequest['classroom_id'],
            ];
            $newStudentEnrollmentID = $this->studentEnrollment->insertNewStudentEnrollment($data);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: 'An error occurred while assigning a new student',
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $newStudentEnrollment = $this->studentEnrollment->getStudentEnrollmentByID($newStudentEnrollmentID);

        //return response
        // return new StudentEnrollmentResource(true, 'New Student-Class added', $classes);
        return $this->apiResponse->successResponse(
            message: 'New student added to the class.',
            data: new StudentEnrollmentResource($newStudentEnrollment),
            codeResponse: 201
        );
    }

    // LMS-13 => move to [LMS-89]
    // CHANGE: move to index() with query parameter classroomID
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

    // LMS-122 => move to [LMS-92]
    // note: student terdaftar di kelas mana aja
    // public function studentClassroom($studentID)
    // {
    //     //get users
    //     // $users = DB::table('student_enrollments')->where('user_id', $request->user_id)->get();
    //     // $users = StudentEnrollment::where('user_id', $request->user_id)->first();

    //     try {
    //         $studentEnrollment = DB::table('student_enrollments')
    //             ->join('users', 'student_enrollments.user_id', '=', 'users.id')
    //             ->join('classrooms', 'student_enrollments.classroom_id', '=', 'classrooms.id')
    //             ->select(
    //                 'student_enrollments.id as id',
    //                 'student_enrollments.classroom_id',
    //                 'student_enrollments.user_id',

    //                 'users.id as user_id',
    //                 'users.name as user_name',
    //                 'users.username as user_username',
    //                 'users.role as user_role',
    //                 'users.avatar as user_avatar',

    //                 'classrooms.id as classroom_id',
    //                 'classrooms.name as classroom_name',
    //                 'classrooms.grade as classroom_grade'
    //             )
    //             ->where('student_enrollments.user_id', $studentID)
    //             ->first();
    //     } catch (\Throwable $th) {
    //         return $this->apiResponse->errorResponse(
    //             message: 'An error occurred while fetching student enrollments',
    //             errors: $th->getMessage(),
    //             codeResponse: 500
    //         );
    //     }

    //     if ($studentEnrollment == null) {
    //         return $this->apiResponse->errorResponse(
    //             message: 'Student Enrollment not found.',
    //             errors: ['Student Enrollment not found'],
    //             codeResponse: 404
    //         );
    //     }

    //     // if ($users == "[]") {
    //     //     return new StudentEnrollmentResource(false, 'No Students found', $users);
    //     // } else {
    //     //     //return collection of users as a resource
    //     //     return new StudentEnrollmentResource(true, 'Students Class', $users->classroom_id);
    //     // }
    //     return $this->apiResponse->successResponse(
    //         message: 'Student Enrollment in classroom retrieved successfully.',
    //         data: new StudentEnrollmentResource($studentEnrollment),
    //         codeResponse: 200
    //     );
    // }

    // LMS-92
    // note: dapetin list student yang belum terdaftar di kelas tersebut (classroomID), biar ga duplicate student yang sama di kelas yang sama
    public function getAvailableStudents($filters)
    {
        if (!is_numeric($filters['classroom_id']) || intval($filters['classroom_id']) != $filters['classroom_id']) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Classroom ID.",
                errors: ['Invalid Classroom ID'],
                codeResponse: 400
            );
        }

        $filtersByClassroomID = ['classroom_id' => $filters['classroom_id']];
        $listOfEnrolledUsers = $this->studentEnrollment->getStudentEnrollmentsByCondition($filtersByClassroomID);
        $classroom = (object) [
            'id' => $listOfEnrolledUsers[0]->classroom_id ?? null,
            'name' => $listOfEnrolledUsers[0]->classroom_name ?? null,
            'grade' => $listOfEnrolledUsers[0]->classroom_grade ?? null,
            'created_at' => $listOfEnrolledUsers[0]->classroom_created_at ?? null,
            'updated_at' => $listOfEnrolledUsers[0]->classroom_updated_at ?? null,
        ];

        $listOfEnrolledUsersID = $listOfEnrolledUsers->pluck('user_id')->toArray();
        $availableStudents = $this->user->getAvailableStudents($listOfEnrolledUsersID);

        $message = "List of available users retrieved successfully.";
        if ($availableStudents->isEmpty()) $message = "No available user found.";

        return $this->apiResponse->successResponse(
            message: $message,
            data: new AvailableStudentsResource($classroom, $availableStudents),
            codeResponse: 200
        );
    }
}
