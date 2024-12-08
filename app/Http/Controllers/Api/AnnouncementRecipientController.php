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
    public function show(AnnouncementRecipient $announcementRecipient)
    {
        //
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
