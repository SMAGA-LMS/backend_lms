<?php

namespace App\Http\Resources\SessionRecordResource;

use App\Http\Resources\ClassEnrollmentResource\ClassEnrollmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $classEnrollment = $this->class_enrollment_id ? (object)[
            'id' => $this->class_enrollment_id,

            'classroom_id' => $this->classroom_id ?? null,
            'classroom_name' => $this->classroom_name ?? null,
            'classroom_grade' => $this->classroom_grade ?? null,
            'classroom_created_at' => $this->classroom_created_at ?? null,
            'classroom_updated_at' => $this->classroom_updated_at ?? null,

            'course_id' => $this->course_id ?? null,
            'course_name' => $this->course_name ?? null,
            'course_grade' => $this->course_grade ?? null,
            'course_created_at' => $this->course_created_at ?? null,
            'course_updated_at' => $this->course_updated_at ?? null,

            'pic_course_id' => $this->pic_course_id ?? null,
            'pic_course_name' => $this->pic_course_name ?? null,
            'pic_course_username' => $this->pic_course_username ?? null,
            'pic_course_role' => $this->pic_course_role ?? null,
            'pic_course_avatar' => $this->pic_course_avatar ?? null,
            'pic_course_created_at' => $this->pic_course_created_at ?? null,
            'pic_course_updated_at' => $this->pic_course_updated_at ?? null,

            'user_id' => $this->teacher_id ?? null,
            'user_name' => $this->teacher_name ?? null,
            'user_username' => $this->teacher_username ?? null,
            'user_role' => $this->teacher_role ?? null,
            'user_avatar' => $this->teacher_avatar ?? null,
            'user_created_at' => $this->teacher_created_at ?? null,
            'user_updated_at' => $this->teacher_updated_at ?? null,

            'created_at' => $this->class_enrollment_created_at ?? null,
            'updated_at' => $this->class_enrollment_updated_at ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,

            'classEnrollment' => $classEnrollment ? new ClassEnrollmentResource($classEnrollment) : null,
            'title' => $this->title ?? null,
            'description' => $this->description ?? null,
            'dateTime' => $this->date_time ?? null,

            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
