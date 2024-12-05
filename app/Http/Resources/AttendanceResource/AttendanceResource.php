<?php

namespace App\Http\Resources\AttendanceResource;

use App\Http\Resources\SessionRecordResource\SessionRecordResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $student = $this->student_id ? (object)[
            'id' => $this->student_id,
            'name' => $this->student_name ?? null,
            'username' => $this->student_username ?? null,
            'role' => $this->student_role ?? null,
            'avatar' => $this->student_avatar ?? null,
            'created_at' => $this->student_created_at ?? null,
            'updated_at' => $this->student_updated_at ?? null,
        ] : null;

        $sessionRecord = $this->session_record_id ? (object)[
            'id' => $this->session_record_id,
            'title' => $this->session_record_title ?? null,
            'description' => $this->session_record_description ?? null,
            'date_time' => $this->session_record_date_time ?? null,

            'class_enrollment_id' => $this->session_record_class_enrollment_id ?? null,
            'classroom_id' => $this->session_record_classroom_id ?? null,
            'course_id' => $this->session_record_course_id ?? null,
            'teacher_id' => $this->session_record_teacher_id ?? null,

            'created_at' => $this->session_record_created_at ?? null,
            'updated_at' => $this->session_record_updated_at ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,
            'status' => $this->status ?? null,

            'student' => $student ? new UserResource($student) : null,
            'sessionRecord' => $sessionRecord ? new SessionRecordResource($sessionRecord) : null,

            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
