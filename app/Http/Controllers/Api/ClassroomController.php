<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassroomResource\ClassroomResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Classroom;
// use App\Http\Resources\ClassroomResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ClassroomController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    //
    public function index()
    {
        //get class
        // CHANGE: ganti nama variable $classes jadi $classrooms
        // samain kayak model, dan table database
        $classrooms = Classroom::all();

        $message = "List of classroom retrieved successfully.";
        if (empty($classrooms)) $message = "No classrooms found.";

        return $this->apiResponse->successResponse(
            message: $message,
            data: ClassroomResource::collection($classrooms),
            codeResponse: 200
        );

        //return collection of users as a resource
        // return new ClassroomResource(true, 'List Data Kelas', $classes);
    }

    public function show($id)
    {
        //find class by ID
        $class = Classroom::find($id);

        //return single post as a resource
        if ($class == null) {
            return new ClassroomResource(false, 'User not found', $class);
        } else {
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
