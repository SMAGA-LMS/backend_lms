<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementReceiverRequest\AddNewAnnouncementReceiverRequest;
use App\Http\Resources\AnnouncementReceiverResource\AnnouncementReceiverResource;
use App\Http\Resources\AnnouncementReceiverResponse\AnnouncementReceiverResponse;
use App\Models\AnnouncementReceiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementReceiverController extends Controller
{
    private $apiResponse;
    private $announcementReceiver;
    private $announcementController;

    public function __construct(ApiResponseHelper $apiResponse, AnnouncementReceiver $announcementReceiver, AnnouncementController $announcementController)
    {
        $this->apiResponse = $apiResponse;
        $this->announcementReceiver = $announcementReceiver;
        $this->announcementController = $announcementController;
    }


    // LMS-149
    public function index(Request $request)
    {
        $filterFields = ['receiver_role'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        //get announcement receiver
        try {
            $announcementReceivers = $this->announcementReceiver->getAnnouncementReceiversByCondition($filters);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve announcement receivers",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of Announcement Receivers";
        if ($announcementReceivers->isEmpty()) $message = "Announcement Receivers not found";

        return $this->apiResponse->successResponse(
            message: $message,
            data: AnnouncementReceiverResource::collection($announcementReceivers),
            codeResponse: 200
        );
    }

    // LMS-154
    public function store(AddNewAnnouncementReceiverRequest $request)
    {
        $validatedRequest = $request->validated();

        DB::beginTransaction();

        try {
            $announcementRequest = [
                'title' => $validatedRequest['title'],
                'description' => $validatedRequest['description'],
                'file' => $validatedRequest['file'] ?? null,
                'author_id' => $validatedRequest['author_id'],
            ];
            $announcement = $this->announcementController->createNewAnnouncement($announcementRequest);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->apiResponse->errorResponse(
                message: "Failed to create announcement receiver",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        // create announcement receiver
        try {
            foreach ($validatedRequest['receiver_roles'] as $role) {
                $announcementReceiverRequest = [
                    'announcement_id' => $announcement->id,
                    'receiver_role' => $role,
                ];
                $announcementReceiverID = $this->announcementReceiver->insertNewAnnouncementReceiver($announcementReceiverRequest);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->apiResponse->errorResponse(
                message: "Failed to create announcement receiver",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        try {
            $filters = [
                'announcement_id' => $announcement->id,
            ];
            $newAnnouncementReceivers = $this->announcementReceiver->getAnnouncementReceiversByCondition($filters);
        } catch (\Throwable $th) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve announcement receivers",
                errors: $th->getMessage(),
                codeResponse: 500
            );
        }

        return $this->apiResponse->successResponse(
            message: "Announcement receiver created",
            data: AnnouncementReceiverResource::collection($newAnnouncementReceivers),
            codeResponse: 201
        );
    }

    // LMS-152
    public function show($announcementReceiverID)
    {
        if (!is_numeric($announcementReceiverID) || intval($announcementReceiverID) != $announcementReceiverID) {
            return $this->apiResponse->errorResponse(
                message: "Invalid announcement receiver ID",
                errors: ['Invalid announcement receiver ID'],
                codeResponse: 400
            );
        }

        try {
            $announcementReceiver = $this->announcementReceiver->getAnnouncementReceiversByID($announcementReceiverID);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve announcement receiver",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        if ($announcementReceiver == null) {
            return $this->apiResponse->errorResponse(
                message: "Announcement Receiver not found",
                errors: ['Announcement Receiver not found'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: "Announcement Receiver found",
            data: new AnnouncementReceiverResource($announcementReceiver),
            codeResponse: 200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnnouncementReceiver $announcementReceiver)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnnouncementReceiver $announcementReceiver)
    {
        //
    }
}
