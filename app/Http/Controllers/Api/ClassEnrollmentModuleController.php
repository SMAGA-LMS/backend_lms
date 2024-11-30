<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Models\ClassEnrollmentModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassEnrollmentModuleResource\ClassEnrollmentModuleResource;

class ClassEnrollmentModuleController extends Controller
{
    private $apiResponse;
    private $classEnrollmentModule;

    public function __construct(ApiResponseHelper $apiResponse, ClassEnrollmentModule $classEnrollmentModule)
    {
        $this->apiResponse = $apiResponse;
        $this->classEnrollmentModule = $classEnrollmentModule;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassEnrollmentModule $classEnrollmentModule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassEnrollmentModule $classEnrollmentModule)
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
