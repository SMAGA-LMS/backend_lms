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
        $classroom = (object)[
            'id' => $this->classroom_id,
            'name' => $this->classroom_name,
            'grade' => $this->classroom_grade,
        ];

        $pic_course = (object)[
            'id' => $this->pic_course_id,
            'name' => $this->pic_course_name,
            'username' => $this->pic_course_username,
            'role' => $this->pic_course_role,
            'avatar' => $this->pic_course_avatar,
        ];

        $course = (object)[
            'id' => $this->course_id,
            'name' => $this->course_name,
            'user' => $pic_course,
            'grade' => $this->course_grade,
        ];

        $user = (object)[
            'id' => $this->user_id,
            'name' => $this->user_name,
            'username' => $this->user_username,
            'role' => $this->user_role,
            'avatar' => $this->user_avatar,
        ];

        $dataResponse = [
            'id' => $this->id,
            'classroom' => new ClassroomResource($classroom),
            'course' => new CourseResource($course),
            'user' => new UserResource($user),
        ];

        return $dataResponse;
    }
}
