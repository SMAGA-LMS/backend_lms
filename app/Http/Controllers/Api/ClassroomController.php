<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClassroomRequest\AddNewClassroomRequest;
use App\Http\Resources\ClassroomResource\ClassroomResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Classroom;
// use App\Http\Resources\ClassroomResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;

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
        try {
            $classrooms = Classroom::all();
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve list of classrooms.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

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
        if (!is_numeric($id)) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Classroom ID",
                errors: ['Invalid Classroom ID'],
                codeResponse: 400
            );
        }

        //find classroom by ID
        try {
            $classroom = Classroom::find($id);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve classroom.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }


        // //return single post as a resource
        // if ($class == null) {
        //     return new ClassroomResource(false, 'User not found', $class);
        // } else {
        //     return new ClassroomResource(true, 'Detail User', $class);
        // }
        if ($classroom == null) {
            return $this->apiResponse->errorResponse(
                message: "Classroom not found.",
                errors: ['Classroom not found'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: "Classroom data retrieved successfully.",
            data: new ClassroomResource($classroom),
            codeResponse: 200
        );
    }

    public function store(AddNewClassroomRequest $request)
    {
        // CHANGE: pindah ke ClassroomRequest\AddNewClassroomRequest
        //define validation rules
        // $validator = Validator::make($request->all(), [
        //     'name'      => 'required',
        //     'grade'     => 'required',
        // ]);

        //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        $validatedNewClassroom = $request->validated();

        //create classroom
        try {
            $classroom = Classroom::create([
                'name'     => $validatedNewClassroom['name'],
                'grade' => $validatedNewClassroom['grade'],
            ]);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to create new classroom.",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        //return response
        // return new ClassroomResource(true, 'New Class added', $classes);
        return $this->apiResponse->successResponse(
            message: "New classroom added.",
            data: new ClassroomResource($classroom),
            codeResponse: 201
        );
    }
}
