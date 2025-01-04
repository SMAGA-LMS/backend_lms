<?php

namespace App\Http\Resources\AnnouncementReceiverResource;

use App\Http\Resources\AnnouncementResource\AnnouncementResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddNewAnnouncementReceiverResource extends JsonResource
{
    private $announcementReceiver;
    private $announcements;

    public function __construct($announcementReceiver, $announcements)
    {
        $this->announcementReceiver = $announcementReceiver;
        $this->announcements = $announcements;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataResponse = [
            'announcementReceiver' => new AnnouncementReceiverResource($this->announcementReceiver),
            'announcements' => $this->announcements ? AnnouncementResource::collection($this->announcements) : null,
        ];

        return $dataResponse;
    }
}
