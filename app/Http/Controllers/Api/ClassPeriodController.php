<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\ClassPeriods\AddNewClassPeriodRequestDto;
use App\Enums\AcademictermEnum;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\AddNewClassPeriodRequest;
use App\Http\Resources\DetailClassPeriodResource;
use App\Http\Resources\ListClassPeriodResource;
use App\Services\ClassPeriodService;
use Illuminate\Http\Request;

class ClassPeriodController
{

    protected $classPeriodService;
    protected $apiResponse;

    public function __construct(ClassPeriodService $classPeriodService, ApiResponseHelper $apiResponse)
    {
        $this->classPeriodService = $classPeriodService;
        $this->apiResponse = $apiResponse;
    }


    public function getClassPeriodList(Request $request)
    {
        $academic_term_id = $request->query('academic_term_id');

        if (!isset($academic_term_id) || $academic_term_id === '') $result = $this->classPeriodService->getAllClassPeriodList();
        else $result = $this->classPeriodService->getSpecificClassPeriodList($academic_term_id);

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: ListClassPeriodResource::collection($result->data),
            codeResponse: $result->codeResponse
        );
    }


    /**
     * Display the specified resource.
     */
    public function show($classPeriodCode)
    {
        $result = $this->classPeriodService->getDetailClassPeriod($classPeriodCode);

        if (!$result->isSuccess) {
            return $this->apiResponse->errorResponse(
                message: $result->message,
                errors: $result->errors,
                codeResponse: $result->codeResponse
            );
        }

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: new DetailClassPeriodResource($result->data),
            codeResponse: $result->codeResponse
        );
    }

    public function addNewClassPeriod(AddNewClassPeriodRequest $request)
    {
        $newClassPeriod = new AddNewClassPeriodRequestDto(
            grade_classroom_id: $request->validated('grade_classroom_id'),
            academic_term_id: $request->validated('academic_term_id'),
            user_id: $request->validated('user_id')
        );

        // nanti harus dibuat validasi juga, inject grade_classroom service
        // dan academic_term service buat validasi apakah grade_classroom_id maupun academic_term_id
        // yang diberi ada atau engga di table mereka

        // oh ternyata karena kita udah pake request custom, bisa langsung
        // exists:grade_classrooms,id

        $result = $this->classPeriodService->addNewClassPeriod($newClassPeriod);

        if (!$result->isSuccess) {
            return $this->apiResponse->errorResponse(
                message: $result->message,
                errors: $result->errors,
                codeResponse: $result->codeResponse
            );
        }

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: new DetailClassPeriodResource($result->data),
            codeResponse: $result->codeResponse
        );
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
