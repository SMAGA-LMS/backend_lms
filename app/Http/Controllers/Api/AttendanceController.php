<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest\AddNewAttendanceRequest;
use App\Http\Resources\AttendanceResource\AttendanceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;

class AttendanceController extends Controller
{
    private $apiResponse;
    private $sessionRecordController;
    private $attendance;

    public function __construct(ApiResponseHelper $apiResponse, SessionRecordController $sessionRecordController, Attendance $attendance)
    {
        $this->apiResponse = $apiResponse;
        $this->sessionRecordController = $sessionRecordController;
        $this->attendance = $attendance;
    }


    // LMS-134
    public function index(Request $request)
    {
        // //get users
        // $attendance = Attendance::all();

        $filterFields = [];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        try {
            $listAttendances = $this->attendance->getAttendancesByCondition($filters);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve list attendances.",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of Attendances";
        if ($listAttendances->isEmpty()) $message = "Attendances not found";


        //return collection of users as a resource
        // return new AttendanceResource(true, 'List Data Attendance', $attendance);

        return $this->apiResponse->successResponse(
            message: $message,
            data: AttendanceResource::collection($listAttendances),
            codeResponse: 200
        );
    }

    // LMS-136, LMS-117
    public function store(AddNewAttendanceRequest $request)
    {
        // CHANGE: move to AddNewAttendanceRequest
        // $validator = Validator::make($request->all(), [
        //     'students'      => 'required|array',
        //     'students.*.student_id' => 'required',
        //     'students.*.class_enrollment_id' => 'required',
        //     'students.*.date_time' => 'required|date',
        //     'students.*.session' => 'required',
        // ]);

        // //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        // $students = $request->all();

        $validatedRequest = $request->validated();

        DB::beginTransaction();

        // create session record
        try {
            $sessionRecordRequest = [
                'class_enrollment_id' => $validatedRequest['class_enrollment_id'],
                'title' => $validatedRequest['title'],
                'description' => $validatedRequest['description'],
                'date_time' => $validatedRequest['date_time'],
            ];
            $newSessionRecord = $this->sessionRecordController->createNewSessionRecord($sessionRecordRequest);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->apiResponse->errorResponse(
                message: "Failed to create new session record.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        // create attendance
        try {
            foreach ($validatedRequest['students'] as $student) {
                $attendanceData = [
                    'student_id' => $student['student_id'],
                    'session_record_id' => $newSessionRecord->id,
                    'status' => $student['status'],
                ];
                $newAttendanceID = $this->attendance->insertNewAttendance($attendanceData);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->apiResponse->errorResponse(
                message: "Failed to insert attendance.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        try {
            $filters = [
                'session_record_id' => $newSessionRecord->id
            ];
            $newListAttendances = $this->attendance->getAttendancesByCondition($filters);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve attendances.",
                errors: $th->getMessage(),
                codeResponse: 404
            );
        }

        // return new AttendanceResource(true, 'New Attendance added', $students);
        return $this->apiResponse->successResponse(
            message: "New attendances added successfully.",
            data: AttendanceResource::collection($newListAttendances),
            codeResponse: 201
        );
    }

    // LMS-135
    public function show($id)
    {
        $module = DB::table('attendances')->where('class_enrollment_id', '=', $id)->get();

        if ($module->isEmpty()) {
            return new AttendanceResource(false, 'No Attendance found', $module);
        } else {
            return new AttendanceResource(true, 'Attendance found', $module);
        }
    }

    // LMS-137, LMS-145
    public function getAttendancesForStudent(Request $request, $userID)
    {
        // $validator = Validator::make($request->all(), [
        //     'student_id' => 'required',
        //     'class_enrollment_id' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        // $studentID = $request->query('student_id');
        $classEnrollmentID = $request->query('class_enrollment_id');

        // $module = DB::table('attendances')->where('class_enrollment_id', '=', $request->class_enrollment_id)->where('student_id', '=', $request->student_id)->get();
        try {
            $listAttendancesStudent = $this->attendance->getAttendancesForStudent($userID, $classEnrollmentID);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve attendances.",
                errors: $th->getMessage(),
                codeResponse: 404
            );
        }

        $message = "List of Attendances for Student";
        if ($listAttendancesStudent->isEmpty()) $message = "Attendances not found";

        // if ($module->isEmpty()) {
        //     return new AttendanceResource(false, 'No Attendance found', $module);
        // } else {
        //     return new AttendanceResource(true, 'Attendance found', $module);
        // }
        return $this->apiResponse->successResponse(
            message: $message,
            data: AttendanceResource::collection($listAttendancesStudent),
            codeResponse: 200
        );
    }
}
