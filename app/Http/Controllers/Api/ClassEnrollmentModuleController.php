<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Models\ClassEnrollmentModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClassEnrollmentModuleRequest\AddNewClassEnrollmentModuleRequest;
use App\Http\Resources\ClassEnrollmentModuleResource\ClassEnrollmentModuleResource;
use Exception;
use Illuminate\Support\Facades\DB;

class ClassEnrollmentModuleController extends Controller
{
    private $apiResponse;
    private $classEnrollmentModule;
    private $moduleController;

    public function __construct(ApiResponseHelper $apiResponse, ClassEnrollmentModule $classEnrollmentModule, ModuleController $moduleController)
    {
        $this->apiResponse = $apiResponse;
        $this->classEnrollmentModule = $classEnrollmentModule;
        $this->moduleController = $moduleController;
    }

    // LMS-113
    public function index(Request $request)
    {
        $filterFields = ['class_enrollment_id'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        //get modules
        try {
            $courseModules = $this->classEnrollmentModule->getClassEnrollmentModulesByCondition($filters);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve class enrollment modules",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of Class Enrollment Modules";
        if ($courseModules->isEmpty()) $message = "Class Enrollment Modules not found";

        //return collection of modules as a resource
        // return new ModuleResource(true, 'List of Modules', $modules);
        return $this->apiResponse->successResponse(
            message: $message,
            data: ClassEnrollmentModuleResource::collection($courseModules),
            codeResponse: 200
        );
    }

    // LMS-131
    public function store(AddNewClassEnrollmentModuleRequest $request)
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

            // Insert into class_enrollment_module
            $data = [
                'class_enrollment_id' => $validatedRequest['class_enrollment_id'],
                'module_id' => $module->id,
            ];
            $newClassEnrollmentModuleID = $this->classEnrollmentModule->insertNewClassEnrollmentModule($data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->apiResponse->errorResponse(
                message: "Failed to add new class enrollment module",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        $newModule = $this->classEnrollmentModule->getClassEnrollmentModuleByID($newClassEnrollmentModuleID);

        return $this->apiResponse->successResponse(
            message: "New Course Module added to course Name: " . $newModule->course_name . " with ID: " . $newModule->course_id,
            data: new ClassEnrollmentModuleResource($newModule),
            codeResponse: 201
        );
    }

    // LMS-133
    public function show($classEnrollmentModuleID)
    {
        if (!is_numeric($classEnrollmentModuleID) || intval($classEnrollmentModuleID) != $classEnrollmentModuleID) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Class Enrollment Module ID",
                errors: ['Invalid Class Enrollment Module ID'],
                codeResponse: 400
            );
        }

        try {
            $classEnrollmentModule = $this->classEnrollmentModule->getClassEnrollmentModuleByID($classEnrollmentModuleID);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve course module",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        if ($classEnrollmentModule == null) {
            return $this->apiResponse->errorResponse(
                message: "Class Enrollment Module not found",
                errors: ['Class Enrollment Module not found'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: "Course Module found",
            data: new ClassEnrollmentModuleResource($classEnrollmentModule),
            codeResponse: 200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassEnrollmentModule $classEnrollmentModule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassEnrollmentModule $classEnrollmentModule)
    {
        //
    }
}
