<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Resources\GradeClassroomResource;
use App\Services\GradeClassroomService;
use Illuminate\Http\Request;

class GradeClassroomController
{

    protected $gradeClassroomService;
    protected $apiResponse;

    public function __construct(GradeClassroomService $gradeClassroomService, ApiResponseHelper $apiResponse)
    {
        $this->gradeClassroomService = $gradeClassroomService;
        $this->apiResponse = $apiResponse;
    }

    public function getGradeClassroomList()
    {
        $result = $this->gradeClassroomService->getAllGradeClassrooms();

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: GradeClassroomResource::collection($result->data),
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
     * Display the specified resource.
     */
    public function show(string $id)
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
