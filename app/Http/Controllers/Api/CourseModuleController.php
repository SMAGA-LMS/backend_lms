<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseModuleRequest\AddNewCourseModuleRequest;
use App\Http\Requests\ModuleRequest\AddNewModuleRequest;
use App\Http\Resources\CourseModuleResource\CourseModuleResource;
use App\Models\CourseModule;
use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\error;

class CourseModuleController extends Controller
{
    private $apiResponse;
    private $moduleController;
    private $courseModule;

    public function __construct(ApiResponseHelper $apiResponse, ModuleController $moduleController, CourseModule $courseModule)
    {
        $this->apiResponse = $apiResponse;
        $this->moduleController = $moduleController;
        $this->courseModule = $courseModule;
    }

    // LMS-109
    public function index(Request $request)
    {
        $filterFields = ['course_id'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        //get modules
        try {
            $courseModules = $this->courseModule->getCourseModulesByCondition($filters);
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

        DB::beginTransaction();

        try {
            // Create module using ModuleController
            // Call the createNewModule method from ModuleController
            $module = $this->moduleController->createNewModule($validatedRequest, $request);
            if ($module == null) {
                throw new Exception("Error Create New Module", 500);
            }

            // Insert into course_module
            $data = [
                'course_id' => $validatedRequest['course_id'],
                'module_id' => $module->id,
            ];
            $newCourseModuleID = $this->courseModule->insertNewCourseModule($data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->apiResponse->errorResponse(
                message: "Failed to add new course module",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $newModule = $this->courseModule->getCourseModuleByID($newCourseModuleID);

        return $this->apiResponse->successResponse(
            message: "New Course Module added to course Name: " . $newModule->course_name . " with ID: " . $newModule->course_id,
            data: new CourseModuleResource($newModule),
            codeResponse: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($courseModuleID)
    {
        if (!is_numeric($courseModuleID) || intval($courseModuleID) != $courseModuleID) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Course Module ID",
                errors: ['Invalid Course Module ID'],
                codeResponse: 400
            );
        }

        try {
            $courseModule = $this->courseModule->getCourseModuleByID($courseModuleID);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve course module",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        if ($courseModule == null) {
            return $this->apiResponse->errorResponse(
                message: "Course Module not found",
                errors: ['Course Module not found'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: "Course Module found",
            data: new CourseModuleResource($courseModule),
            codeResponse: 200
        );
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
