<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseModuleRequest\AddNewCourseModuleRequest;
use App\Http\Requests\ModuleRequest\AddNewModuleRequest;
use App\Http\Resources\CourseModuleResource\CourseModuleResource;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\error;

class CourseModuleController extends Controller
{
    protected $apiResponse;
    protected $moduleController;

    public function __construct(ApiResponseHelper $apiResponse, ModuleController $moduleController)
    {
        $this->apiResponse = $apiResponse;
        $this->moduleController = $moduleController;
    }

    // LMS-109
    public function index(Request $request)
    {
        $courseID = $request->query('courseID');

        //get modules
        try {
            $courseModulesQuery = DB::table('course_modules')
                ->join('modules', 'course_modules.module_id', '=', 'modules.id')
                ->join('courses', 'course_modules.course_id', '=', 'courses.id')
                ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
                ->select(
                    'course_modules.id',

                    'modules.id as module_id',
                    'modules.name as module_name',
                    'modules.description as module_description',
                    'modules.file as module_file',

                    'courses.id as course_id',
                    'courses.name as course_name',
                    'courses.user_id as course_user_id',
                    'courses.grade as course_grade',

                    'pic_courses.id as pic_course_id',
                    'pic_courses.name as pic_course_name',
                    'pic_courses.username as pic_course_username',
                    'pic_courses.role as pic_course_role',
                    'pic_courses.avatar as pic_course_avatar',
                );

            if (!empty($courseID)) {
                $courseModulesQuery->where('course_id', $courseID);
            }

            $courseModules = $courseModulesQuery->get();
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve course modules",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of Starter Modules";
        if ($courseModules->isEmpty()) $message = "Starter Modules not found";

        //return collection of modules as a resource
        // return new ModuleResource(true, 'List of Modules', $modules);
        return $this->apiResponse->successResponse(
            message: $message,
            data: CourseModuleResource::collection($courseModules),
            codeResponse: 200
        );
    }

    // LMS-107
    public function store(AddNewCourseModuleRequest $request)
    {
        $validatedRequest = $request->validated();

        // Create module using ModuleController
        try {
            // Call the createNewModule method from ModuleController
            $module = $this->moduleController->createNewModule($validatedRequest, $request);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "An error occurred while creating module",
                errors: [$e->getMessage()],
                codeResponse: 500
            );
        }

        // Insert into course_module
        // ini belum return data join ke table course, module, jadi return response nya masih table course module aja
        // object course ada, tapi ke isi yang course.id aja, kalau course.name, dll pasti null value nya (karena belum di-join)
        $courseModule = CourseModule::create([
            'course_id' => $validatedRequest['courseID'],
            'module_id' => $module->id,
        ]);

        return $this->apiResponse->successResponse(
            message: "New Course Module added to course ID: " . $courseModule->course_id,
            data: new CourseModuleResource($courseModule),
            codeResponse: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
