<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest\AddNewModuleRequest;
use App\Http\Resources\ModuleResource\ModuleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ModuleController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function index()
    {
        // $courseID = $request->query('courseID');

        //get modules
        try {
            $modules = Module::all();

            // read database/migrations/2024_08_07_130656_create_modules_table.php
            // $modulesQuery = DB::table('modules')
            // ->join('courses', 'modules.course_id', '=', 'courses.id')
            // ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            // ->select(
            //     'modules.id',
            //     'modules.name',
            //     'modules.description',
            //     'modules.file',

            //     'courses.id as course_id',
            //     'courses.name as course_name',
            //     'courses.user_id as course_user_id',
            //     'courses.grade as course_grade',

            //     'pic_courses.id as pic_course_id',
            //     'pic_courses.name as pic_course_name',
            //     'pic_courses.username as pic_course_username',
            //     'pic_courses.role as pic_course_role',
            //     'pic_courses.avatar as pic_course_avatar',
            // );

            // if (!empty($courseID)) {
            //     $modulesQuery->where('course_id', $courseID);
            // }

            // $modules = $modulesQuery->get();
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve modules",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of Modules";
        if ($modules->isEmpty()) $message = "Modules not found";

        //return collection of modules as a resource
        // return new ModuleResource(true, 'List of Modules', $modules);
        return $this->apiResponse->successResponse(
            message: $message,
            data: ModuleResource::collection($modules),
            codeResponse: 200
        );
    }

    public function store(AddNewModuleRequest $request)
    {
        // CHANGE: move to AddNewModuleRequest
        // //define validation rules
        // $validator = Validator::make($request->all(), [
        //     'name'      => 'required',
        //     'description'      => 'required',
        //     'file'     => 'mimes:xlsx,doc,docx,ppt,pptx,pdf',
        // ]);

        // //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        $validatedRequest = $request->validated();
        $module = $this->createNewModule($validatedRequest, $request);

        // return new ModuleResource(true, 'New Module added', $modules);
        return $this->apiResponse->successResponse(
            message: "New Module added",
            data: new ModuleResource($module),
            codeResponse: 201
        );
    }

    public function createNewModule($validatedRequest, $request)
    {
        //upload image
        if ($request->hasFile('file')) {
            $modulefile = $request->file('file');
            $modulefile->storeAs('public/Modules', $modulefile->hashName());
            $moduleDb = $modulefile->hashName();
        } else {
            $moduleDb = null;
        }

        //create module
        return Module::create([
            'name'     => $validatedRequest['name'],
            'description'   => $validatedRequest['description'],
            'file'     => $moduleDb,
            // 'course_id' => $validatedRequest['courseID'] // read database/migrations/2024_08_07_130656_create_modules_table.php
        ]);
    }

    public function show($id)
    {
        if (!is_numeric($id) || intval($id) != $id) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Module ID",
                errors: ['Invalid Module ID'],
                codeResponse: 400
            );
        }

        //find post by ID
        try {
            $module = Module::find($id);

            // read database/migrations/2024_08_07_130656_create_modules_table.php
            // $module = DB::table('modules')
            //     ->join('courses', 'modules.course_id', '=', 'courses.id')
            //     ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            //     ->select(
            //         'modules.id',
            //         'modules.name',
            //         'modules.description',
            //         'modules.file',

            //         'courses.id as course_id',
            //         'courses.name as course_name',
            //         'courses.user_id as course_user_id',
            //         'courses.grade as course_grade',

            //         'pic_courses.id as pic_course_id',
            //         'pic_courses.name as pic_course_name',
            //         'pic_courses.username as pic_course_username',
            //         'pic_courses.role as pic_course_role',
            //         'pic_courses.avatar as pic_course_avatar',
            //     )
            //     ->where('modules.id', $id)
            //     ->first();
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve module",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "Module retrieved successfully.";
        if (empty($module)) $message = "Module not found.";

        if ($module == null) {
            return $this->apiResponse->errorResponse(
                message: $message,
                errors: [$message],
                codeResponse: 404
            );
        }

        //return single post as a resource
        // if ($module == null) {
        //     return new ModuleResource(false, 'Module not found', $module);
        // } else {
        //     return new ModuleResource(true, 'Detail module', $module);
        // }
        return $this->apiResponse->successResponse(
            message: $message,
            data: new ModuleResource($module),
            codeResponse: 200
        );
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'description'      => 'required',
            'file'     => 'mimes:xlsx,doc,docx,ppt,pptx,pdf',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $module = Module::find($id);

        if (!empty($module)) {
            //upload image
            if ($request->hasFile('file')) {
                $modulefile = $request->file('file');
                $modulefile->storeAs('public/Modules', $modulefile->hashName());
                $moduleDb = $modulefile->hashName();
            } else {
                $moduleDb = "null";
            }
            $request->merge(['file' => $moduleDb]);

            $module->update([
                'name' => $request->name,
                'description' => $request->description,
                'file' => $moduleDb,
                'course_id' => $request->course_id
            ]);
        } elseif (empty($module)) {
            return new ModuleResource(false, 'Module Not Found', $module);
        }

        return new ModuleResource(true, 'Updated Module', $module);
    }
}
