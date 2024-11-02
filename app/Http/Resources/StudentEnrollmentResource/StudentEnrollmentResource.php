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
        $responseData = [
            'id' => $this->resource['id'],
            'classroom' => new ClassroomResource((object)[
                'id' => $this->resource['classroom_id'],
                'name' => $this->resource['classroom_name'],
                'grade' => $this->resource['classroom_grade'],
            ]),
            'user' => new UserResource((object)[
                'id' => $this->resource['user_id'],
                'name' => $this->resource['user_name'],
                'username' => $this->resource['user_username'],
                'role' => $this->resource['user_role'],
                'avatar' => $this->resource['user_avatar'],
            ]),
        ];

        return $responseData;
    }
}
