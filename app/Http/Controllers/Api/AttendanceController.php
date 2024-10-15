<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Http\Resources\AttendanceResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AttendanceController extends Controller
{
    //
    public function index()
    {
        //get users
        $attendance = Attendance::all();

        //return collection of users as a resource
        return new AttendanceResource(true, 'List Data Attendance', $attendance);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'students'      => 'required|array',
            'students.*.student_id' => 'required',
            'students.*.classenrollment_id' => 'required',
            'students.*.date_time' => 'required|date',
            'students.*.session' => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $students = $request->all();

        foreach($students['students'] as $items){
            Attendance::create([
                'student_id' => $items['student_id'],
                'classenrollment_id' => $items['classenrollment_id'],
                'date_time' => $items['date_time'],
                'session' => $items['session'],
            ]);
        }

        return new AttendanceResource(true, 'New Attendance added', $students);
    }

    public function show($id){
        $module = DB::table('attendances')->where('classenrollment_id', '=', $id)->get();

        if($module->isEmpty()){
            return new AttendanceResource(false, 'No Attendance found', $module);
        }
        else{
            return new AttendanceResource(true, 'Attendance found', $module);
        }
    }

    public function student_ce(Request $request){

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'classenrollment_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $module = DB::table('attendances')->where('classenrollment_id', '=', $request->classenrollment_id)->where('student_id', '=', $request->student_id)->get();

        if($module->isEmpty()){
            return new AttendanceResource(false, 'No Attendance found', $module);
        }
        else{
            return new AttendanceResource(true, 'Attendance found', $module);
        }
    }

}
