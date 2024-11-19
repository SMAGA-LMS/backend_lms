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
            'name' => $this->classroom_name,
            'grade' => $this->classroom_grade,
        ] : null;

        $pic_course = $this->pic_course_id ? (object)[
            'id' => $this->pic_course_id,
            'name' => $this->pic_course_name,
            'username' => $this->pic_course_username,
            'role' => $this->pic_course_role,
            'avatar' => $this->pic_course_avatar,
        ] : null;

        $course = $this->course_id ? (object)[
            'id' => $this->course_id,
            'name' => $this->course_name,
            'user' => $pic_course,
            'grade' => $this->course_grade,
        ] : null;

        $user = $this->user_id ? (object)[
            'id' => $this->user_id,
            'name' => $this->user_name,
            'username' => $this->user_username,
            'role' => $this->user_role,
            'avatar' => $this->user_avatar,
        ] : null;

        $dataResponse = [
            'id' => $this->id,
            'classroom' => $classroom ? new ClassroomResource($classroom) : null,
            'course' => $course ? new CourseResource($course) : null,
            'user' => $user ? new UserResource($user) : null,
        ];

        return $dataResponse;
    }
}
