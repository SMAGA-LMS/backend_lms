<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest\AddNewAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\AttendanceResource\AddNewAttendanceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AttendanceController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }


    //
    public function index()
    {
        //get users
        $attendance = Attendance::all();

        //return collection of users as a resource
        return new AttendanceResource(true, 'List Data Attendance', $attendance);
    }

    public function store(AddNewAttendanceRequest $request)
    {
        // CHANGE: move to AddNewAttendanceRequest
        // $validator = Validator::make($request->all(), [
        //     'students'      => 'required|array',
        //     'students.*.student_id' => 'required',
        //     'students.*.classenrollment_id' => 'required',
        //     'students.*.date_time' => 'required|date',
        //     'students.*.session' => 'required',
        // ]);

        // //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        $validatedRequest = $request->validated();
        $students = $validatedRequest;

        // $students = $request->all();

        // create attendance
        try {
            $createdAttendances = [];
            foreach ($students['students'] as $items) {
                // Simpan attendance berdasarkan studentID sebagai key
                $createdAttendances[$items['student_id']] = Attendance::create([
                    'student_id' => $items['student_id'],
                    'classenrollment_id' => $items['classenrollment_id'],
                    'date_time' => $items['date_time'],
                    'session' => $items['session'],
                ]);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to insert attendance.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        // return new AttendanceResource(true, 'New Attendance added', $students);
        return $this->apiResponse->successResponse(
            message: "New attendance added successfully.",
            data: $createdAttendances,
            codeResponse: 201
        );
    }

    public function show($id)
    {
        $module = DB::table('attendances')->where('classenrollment_id', '=', $id)->get();

        if ($module->isEmpty()) {
            return new AttendanceResource(false, 'No Attendance found', $module);
        } else {
            return new AttendanceResource(true, 'Attendance found', $module);
        }
    }

    public function student_ce(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'classenrollment_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $module = DB::table('attendances')->where('classenrollment_id', '=', $request->classenrollment_id)->where('student_id', '=', $request->student_id)->get();

        if ($module->isEmpty()) {
            return new AttendanceResource(false, 'No Attendance found', $module);
        } else {
            return new AttendanceResource(true, 'Attendance found', $module);
        }
    }
}
