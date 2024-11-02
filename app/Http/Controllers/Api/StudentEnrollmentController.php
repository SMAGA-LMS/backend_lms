<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\ResponseDto;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentEnrollmentRequest\GetAvailableStudentsByClassroomRequest;
use App\Http\Resources\StudentEnrollmentResource\ListStudentEnrolledClassroomResource;
use App\Http\Resources\StudentEnrollmentResource\StudentEnrollmentResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class StudentEnrollmentController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    // pakai query param /student-enrollments?classroomID=classroomID
    public function index(Request $request)
    {
        $classroomID = $request->query('classroomID');

        if (!isset($classroomID) || $classroomID === '') $result = $this->getAllStudentEnrollmentList();
        else $result = $this->getListStudentsByClassroomID($classroomID);

        // move to getAllStudentEnrollmentList()
        // //get class
        // $classes = StudentEnrollment::all();

        $enrollments = $result->data;

        if ($result->isSuccess === false) {
            return $this->apiResponse->errorResponse(
                message: $result->message,
                errors: $result->errors,
                codeResponse: $result->codeResponse
            );
        }

        // TODO: karena ga main DTO, jadi harus cek via key 'classroom' atau istilah nya bermain dengan key map
        if (empty($enrollments)) {
            return $this->apiResponse->successResponse(
                message: $result->message,
                data: $enrollments,
                codeResponse: $result->codeResponse
            );
        }

        // //return collection of users as a resource
        // return new StudentEnrollmentResource(true, 'List Data Student-Class', $classes);
        return $this->apiResponse->successResponse(
            message: $result->message,
            data: StudentEnrollmentResource::collection($enrollments),
            codeResponse: $result->codeResponse
        );
    }

    public function show($id)
    {
        dd($id);
        return null;
    }

    public function getAllStudentEnrollmentList(): ResponseDto
    {
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
            return new ResponseDto(
                isSuccess: false,
                message: 'An error occurred while fetching student enrollments',
                data: null,
                errors: $th->getMessage(),
                codeResponse: 500,
            );
        }

        $message = "List of users retrieved successfully.";
        if (empty($studentEnrollments)) $message = "No users found.";

        return new ResponseDto(
            isSuccess: true,
            message: $message,
            data: $studentEnrollments,
            errors: null,
            codeResponse: 200
        );
    }

    public function getListStudentsByClassroomID(string $classroomID): ResponseDto
    {
        if (!is_numeric($classroomID) || intval($classroomID) != $classroomID) {
            return new ResponseDto(
                isSuccess: false,
                message: "Invalid Classroom ID.",
                data: null,
                errors: ['Invalid Classroom ID'],
                codeResponse: 400
            );
        }

        $classroom = DB::table('classrooms')->where('id', intval($classroomID))->first();
        if ($classroom == null) {
            return new ResponseDto(
                isSuccess: false,
                message: "Classroom not found.",
                data: null,
                errors: ['Classroom not found'],
                codeResponse: 404
            );
        }

        $studentEnrollments = StudentEnrollment::where('classroom_id', $classroomID)
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
            return new ResponseDto(
                isSuccess: true,
                message: "No" . " student " . "found." . " in classroom " . $classroomID,
                data: [],
                codeResponse: 200
            );
        }

        return new ResponseDto(
            isSuccess: true,
            message: "List of" . " students " . "retrieved successfully.",
            data: $studentEnrollments,
            codeResponse: 200
        );
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required',
            'classroom_id'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create class
        $classes = StudentEnrollment::create([
            'user_id'     => $request->user_id,
            'classroom_id' => $request->classroom_id,
        ]);

        //return response
        return new StudentEnrollmentResource(true, 'New Student-Class added', $classes);
    }

    // CHANGE: move to getListStudentsByClassroomID()
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

    // note: dapetin list student yang belum terdaftar di kelas tersebut (classroomID), biar ga duplicate student yang sama di kelas yang sama
    public function getAvailableStudents(GetAvailableStudentsByClassroomRequest $request)
    {
        $validatedRequest = $request->validated();
        $classroomID = $validatedRequest['classroomID'];

        // if (!is_numeric($classroomID) || intval($classroomID) != $classroomID) {
        //     return $this->apiResponse->errorResponse(
        //         message: "Invalid Classroom ID.",
        //         errors: ['Invalid Classroom ID'],
        //         codeResponse: 400
        //     );
        // }

        $listOfEnrolledUsersID = StudentEnrollment::where('classroom_id', $classroomID)->pluck('user_id');

        $availableUsers = User::whereNotIn('id', $listOfEnrolledUsersID)->get();

        $message = "List of users not in classroom retrieved successfully.";
        if ($availableUsers->isEmpty()) $message = "No available user found.";

        return $this->apiResponse->successResponse(
            message: $message,
            data: UserResource::collection($availableUsers),
            codeResponse: 200
        );
    }
}
