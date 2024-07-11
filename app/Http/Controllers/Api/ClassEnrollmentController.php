<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ClassEnrollment;
use App\Http\Resources\ClassEnrollmentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ClassEnrollmentController extends Controller
{
    public function index()
    {
        //get class
        $courses = ClassEnrollment::all();

        //return collection of users as a resource
        return new ClassEnrollmentResource(true, 'List Data Course-Class', $courses);
    }

    public function show($id)
    {
        //find class by ID
        $classenr = ClassEnrollment::find($id);

        //return single post as a resource
        if($classenr==null){
            return new ClassEnrollmentResource(false, 'Class-Course not found', $classenr);
        }
        else{
            return new ClassEnrollmentResource(true, 'Detail Class-Course', $classenr);
        }

    }

    public function getCoursesClassID(Request $request)
    {
        $classes = DB::table('class_enrollments')->where('course_id', $request->course_id)->get()->first();

        if($classes == null){
            return new ClassEnrollmentResource(false, 'No Course or Classes found', $classes);
        }
        else{
            //return collection of classes as a resource
            return new ClassEnrollmentResource(true, 'List Classes in Course', $classes);
        }

    }

    public function getCoursesClassIDList(Request $request)
    {
        $classes = DB::table('class_enrollments')->where('course_id', $request->course_id)->get();

        if($classes == "[]"){
            return new ClassEnrollmentResource(false, 'No Course or Classes found', $classes);
        }
        else{
            //return collection of classes as a resource
            return new ClassEnrollmentResource(true, 'List Classes in Course', $classes);
        }

    }

    public function getClassesCourseID(Request $request)
    {
        $courses = DB::table('class_enrollments')->where('classroom_id', $request->classroom_id)->get();

        if($courses == "[]"){
            return new ClassEnrollmentResource(false, 'No Classes found', $courses);
        }
        else{
            //return collection of classes as a resource
            return new ClassEnrollmentResource(true, 'List Courses in a Class', $courses);
        }

    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'course_id'      => 'required',
            'classroom_id'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create class
        $classes = ClassEnrollment::create([
            'course_id'     => $request->course_id,
            'classroom_id' => $request->classroom_id,
        ]);

        //return response
        return new ClassEnrollmentResource(true, 'New Class-Course added', $classes);
    }

}
