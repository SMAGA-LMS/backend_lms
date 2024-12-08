<?php

namespace App\Http\Resources\AnnouncementReceiverResource;

use App\Http\Resources\AnnouncementResource\AnnouncementResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementReceiverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $announcement = $this->announcement_id ? (object)[
            'id' => $this->announcement_id,
            'title' => $this->announcement_title ?? null,
            'description' => $this->announcement_description ?? null,
            'file' => $this->announcement_file ?? null,
            'created_at' => $this->announcement_created_at ?? null,
            'updated_at' => $this->announcement_updated_at ?? null,

            'author_id' => $this->announcement_author_id ?? null,
            'author_name' => $this->announcement_author_name ?? null,
            'author_username' => $this->announcement_author_username ?? null,
            'author_role' => $this->announcement_author_role ?? null,
            'author_avatar' => $this->announcement_author_avatar ?? null,
            'author_created_at' => $this->announcement_author_created_at ?? null,
            'author_updated_at' => $this->announcement_author_updated_at ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,
            'announcement' => $announcement ? new AnnouncementResource($announcement) : null,
            'receiverRole' => $this->receiver_role ?? null,

            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
