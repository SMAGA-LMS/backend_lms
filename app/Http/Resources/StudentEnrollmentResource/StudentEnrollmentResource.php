<?php

namespace App\Http\Resources\StudentEnrollmentResource;

use App\Http\Resources\ClassroomResource\ClassroomResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $classroom = $this->classroom_id ? (object)[
            'id' => $this->classroom_id,
            'name' => $this->classroom_name ?? null,
            'grade' => $this->classroom_grade ?? null,
        ] : null;

        $user = $this->user_id ? (object)[
            'id' => $this->user_id,
            'name' => $this->user_name ?? null,
            'username' => $this->user_username ?? null,
            'role' => $this->user_role ?? null,
            'avatar' => $this->user_avatar ?? null,
        ] : null;

        $responseData = [
            'id' => $this->id,

            'classroom' => $classroom ? new ClassroomResource($classroom) : null,
            'user' => $user ? new UserResource($user) : null,

            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];

        return $responseData;
    }
}
