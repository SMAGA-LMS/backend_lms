<?php

namespace App\Http\Resources\AnnouncementResource;

use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $author = $this->author_id ? (object)[
            'id' => $this->author_id,
            'name' => $this->author_name ?? null,
            'username' => $this->author_username ?? null,
            'role' => $this->author_role ?? null,
            'avatar' => $this->author_avatar ?? null,
            'created_at' => $this->author_created_at ?? null,
            'updated_at' => $this->author_updated_at ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'description' => $this->description ?? null,
            'file' => $this->file ? url('storage/Announcements/' . $this->file) : null,
            'author' => $author ? new UserResource($author) : null,

            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
