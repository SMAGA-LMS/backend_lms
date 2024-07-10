<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StudentEnrollment;
use App\Http\Resources\StudentEnrollmentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class StudentEnrollmentController extends Controller
{
    public function index()
    {
        //get class
        $classes = StudentEnrollment::all();

        //return collection of users as a resource
        return new StudentEnrollmentResource(true, 'List Data Student-Class', $classes);
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

    public function studentList(Request $request)
    {
        //get users
        $users = DB::table('student_enrollments')->where('classroom_id', $request->classroom_id)->get();

        if($users == "[]"){
            return new StudentEnrollmentResource(false, 'No Students found', $users);
        }
        else{
            //return collection of users as a resource
            return new StudentEnrollmentResource(true, 'List Student in Class', $users);
        }

    }

    public function studentClassroom(Request $request)
    {
        //get users
        // $users = DB::table('student_enrollments')->where('user_id', $request->user_id)->get();
        $users = StudentEnrollment::where('user_id', $request->user_id)->first();

        if($users == "[]"){
            return new StudentEnrollmentResource(false, 'No Students found', $users);
        }
        else{
            //return collection of users as a resource
            return new StudentEnrollmentResource(true, 'Students Class', $users->classroom_id);
        }

    }
}
