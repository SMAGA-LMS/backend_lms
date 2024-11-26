<?php

namespace App\Http\Resources\CourseResource;

use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user_id ? (object)[
            'id' => $this->user_id,
            'name' => $this->user_name ?? null,
            'username' => $this->user_username ?? null,
            'role' => $this->user_role ?? null,
            'avatar' => $this->user_avatar ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,

            'name' => $this->name,
            'grade' => $this->grade,
            'user' => $user ? new UserResource($user) : null,

            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
