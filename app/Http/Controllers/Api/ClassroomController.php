<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Classroom;
use App\Http\Resources\ClassroomResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ClassroomController extends Controller
{
    //
    public function index()
    {
        //get class
        $classes = Classroom::all();

        //return collection of users as a resource
        return new ClassroomResource(true, 'List Data Kelas', $classes);
    }

    public function show($id)
    {
        //find class by ID
        $class = Classroom::find($id);

        //return single post as a resource
        if($class==null){
            return new ClassroomResource(false, 'User not found', $class);
        }
        else{
            return new ClassroomResource(true, 'Detail User', $class);
        }

    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'grade'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create class
        $classes = Classroom::create([
            'name'     => $request->name,
            'grade' => $request->grade,
        ]);

        //return response
        return new ClassroomResource(true, 'New Class added', $classes);
    }
}
