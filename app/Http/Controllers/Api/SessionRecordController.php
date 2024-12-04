<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SessionRecordRequest\AddNewSessionRecordRequest;
use App\Http\Resources\SessionRecordResource\SessionRecordResource;
use App\Models\SessionRecord;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class SessionRecordController extends Controller
{
    private $apiResponse;
    private $sessionRecord;

    public function __construct(ApiResponseHelper $apiResponse, SessionRecord $sessionRecord)
    {
        $this->apiResponse = $apiResponse;
        $this->sessionRecord = $sessionRecord;
    }

    // LMS-141
    public function index(Request $request)
    {
        $filterFields = ['class_enrollment_id'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        try {
            $sessionRecords = $this->sessionRecord->getSessionRecordsByCondition($filters);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve session records.",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of Session Records";
        if ($sessionRecords->isEmpty()) $message = "Session Records not found";

        return $this->apiResponse->successResponse(
            message: $message,
            data: SessionRecordResource::collection($sessionRecords),
            codeResponse: 200
        );
    }

    // LMS-139
    public function store(AddNewSessionRecordRequest $request)
    {
        $validatedRequest = $request->validated();

        try {
            $sessionRecord = $this->createNewSessionRecord($validatedRequest);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to add new session record.",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        return $this->apiResponse->successResponse(
            message: "New session record added successfully.",
            data: new SessionRecordResource($sessionRecord),
            codeResponse: 201
        );
    }

    // bagian dari LMS-139
    public function createNewSessionRecord(array $validatedRequest)
    {
        $data = [
            'class_enrollment_id' => $validatedRequest['class_enrollment_id'],
            'title' => $validatedRequest['title'],
            'description' => $validatedRequest['description'],
            'date_time' => $validatedRequest['date_time'],
        ];

        try {
            $sessionRecordID = $this->sessionRecord->insertNewSessionRecord($data);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create new session record: " . $e->getMessage());
        }

        $sessionRecord = $this->sessionRecord->getSessionRecordDetailByID($sessionRecordID);
        return $sessionRecord;
    }

    // LMS-143
    public function show(string $sessionRecordID)
    {
        if (!is_numeric($sessionRecordID) || intval($sessionRecordID) != $sessionRecordID) {
            return $this->apiResponse->errorResponse(
                message: "Invalid Session Record ID",
                errors: ['Invalid Session Record ID'],
                codeResponse: 400
            );
        }

        try {
            $sessionRecord = $this->sessionRecord->getSessionRecordDetailByID($sessionRecordID);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve session record",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        if ($sessionRecord == null) {
            return $this->apiResponse->errorResponse(
                message: "Session Record not found",
                errors: ['Session Record not found'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: "Session Record found",
            data: new SessionRecordResource($sessionRecord),
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
