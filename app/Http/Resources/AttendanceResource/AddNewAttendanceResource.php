<?php

namespace App\Http\Resources\AttendanceResource;

use App\Http\Resources\ClassEnrollmentResource\ClassEnrollmentResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddNewAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // masih ga bisa
        $resource = is_array($this->resource) ? json_decode(json_encode($this->resource)) : $this->resource;

        $student = $resource->student_id ? (object) [
            'id' => $resource->student_id,
            'name' => $resource->student_name ?? null,
            'username' => $resource->student_username ?? null,
            'role' => $resource->student_role ?? null,
            'avatar' => $resource->student_avatar ?? null,
        ] : null;

        $classEnrollment = $resource->class_enrollment_id ? (object) [
            'id' => $resource->class_enrollment_id,
            // 'classroom' => $this->student_name,
            // 'course' => $this->student_username,
            // 'user' => $this->student_role,
        ] : null;

        $dataResponse = [
            'id' => $resource->id ?? null,
            'student' => $student ? new UserResource($student) : null,
            'classEnrollment' => $classEnrollment ? new ClassEnrollmentResource($classEnrollment) : null,
            'dateTime' => $resource->date_time ?? null,
            'session' => $resource->session ?? null,
        ];

        return $dataResponse;
    }
}
