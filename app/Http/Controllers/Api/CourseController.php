<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CourseController extends Controller
{
    //
    public function index()
    {
        //get class
        $courses = Course::all();

        //return collection of users as a resource
        return new CourseResource(true, 'List Course', $courses);
    }

    public function show($id)
    {
        //find post by ID
        $course = Course::find($id);

        //return single post as a resource
        if($course==null){
            return new CourseResource(false, 'Course not found', $course);
        }
        else{
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

        if($course == "[]"){
            return new CourseResource(false, 'No Courses found', $course);
        }
        else{
            //return collection of users as a resource
            return new CourseResource(true, 'Courses with desired teacher', $course);
        }

    }
}
