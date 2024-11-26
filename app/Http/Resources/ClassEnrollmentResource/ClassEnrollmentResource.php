<?php

namespace App\Http\Resources\ClassEnrollmentResource;

use App\Http\Resources\ClassroomResource\ClassroomResource;
use App\Http\Resources\CourseResource\CourseResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassEnrollmentResource extends JsonResource
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

        $course = $this->course_id ? (object)[
            'id' => $this->course_id,
            'name' => $this->course_name ?? null,
            'grade' => $this->course_grade ?? null,

            'user_id' => $this->pic_course_id ?? null,
            'user_name' => $this->pic_course_name ?? null,
            'user_username' => $this->pic_course_username ?? null,
            'user_role' => $this->pic_course_role ?? null,
            'user_avatar' => $this->pic_course_avatar ?? null,
        ] : null;

        $user = $this->user_id ? (object)[
            'id' => $this->user_id,
            'name' => $this->user_name ?? null,
            'username' => $this->user_username ?? null,
            'role' => $this->user_role ?? null,
            'avatar' => $this->user_avatar ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,

            'classroom' => $classroom ? new ClassroomResource($classroom) : null,
            'course' => $course ? new CourseResource($course) : null,
            'user' => $user ? new UserResource($user) : null,

            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
