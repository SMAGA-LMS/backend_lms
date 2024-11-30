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

    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(ClassEnrollmentModule $classEnrollmentModule)
    {
        //
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
