<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementRecipientResource\AnnouncementRecipientResource;
use App\Models\AnnouncementRecipient;
use Illuminate\Http\Request;

class AnnouncementRecipientController extends Controller
{
    private $apiResponse;
    private $announcementRecipient;

    public function __construct(ApiResponseHelper $apiResponse, AnnouncementRecipient $announcementRecipient)
    {
        $this->apiResponse = $apiResponse;
        $this->announcementRecipient = $announcementRecipient;
    }

    // LMS-xxx
    public function index(Request $request)
    {
        $filterFields = ['recipient_role'];
        $filters = [];

        foreach ($filterFields as $field) {
            if ($request->query($field)) {
                $filters[$field] = $request->query($field);
            }
        }

        //get announcement recipients
        try {
            $announcementRecipients = $this->announcementRecipient->getAnnouncementRecipientsByCondition($filters);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve announcement recipients",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        $message = "List of Announcement Recipients";
        if ($announcementRecipients->isEmpty()) $message = "Announcement Recipients not found";

        return $this->apiResponse->successResponse(
            message: $message,
            data: AnnouncementRecipientResource::collection($announcementRecipients),
            codeResponse: 200
        );
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
    public function show($announcementRecipientID)
    {
        if (!is_numeric($announcementRecipientID) || intval($announcementRecipientID) != $announcementRecipientID) {
            return $this->apiResponse->errorResponse(
                message: "Invalid announcement recipient ID",
                errors: ['Invalid announcement recipient ID'],
                codeResponse: 400
            );
        }

        try {
            $announcementRecipient = $this->announcementRecipient->getAnnouncementRecipientsByID($announcementRecipientID);
        } catch (\Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to retrieve announcement recipient",
                errors: $e->getMessage(),
                codeResponse: 500
            );
        }

        if ($announcementRecipient == null) {
            return $this->apiResponse->errorResponse(
                message: "Announcement Recipient not found",
                errors: ['Announcement Recipient not found'],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: "Announcement Recipient found",
            data: new AnnouncementRecipientResource($announcementRecipient),
            codeResponse: 200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnnouncementRecipient $announcementRecipient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnnouncementRecipient $announcementRecipient)
    {
        //
    }
}
