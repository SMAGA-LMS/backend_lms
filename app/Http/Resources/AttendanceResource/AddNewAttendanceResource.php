<?php

namespace App\Http\Resources\AttendanceResource;

use App\Http\Resources\SessionRecordResource\SessionRecordResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// ga jadi dipakai (belum tau, sebelumnya untuk add new attendance response, tapi kayaknya better langsung collection dari attendances)
class AddNewAttendanceResource extends JsonResource
{
    private $sessionRecord;
    private $attendances;

    public function __construct($sessionRecord, $attendances)
    {
        $this->sessionRecord = $sessionRecord;
        $this->attendances = $attendances;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sessionRecord = $this->sessionRecord->id ? (object)[
            'id' => $this->sessionRecord->id,
            'title' => $this->sessionRecord->title ?? null,
            'description' => $this->sessionRecord->description ?? null,
            'date_time' => $this->sessionRecord->date_time ?? null,

            'class_enrollment_id' => $this->sessionRecord->class_enrollment_id ?? null,
            'classroom_id' => $this->sessionRecord->classroom_id ?? null,
            'course_id' => $this->sessionRecord->course_id ?? null,
            'teacher_id' => $this->sessionRecord->teacher_id ?? null,

            'created_at' => $this->sessionRecord->created_at ?? null,
            'updated_at' => $this->sessionRecord->updated_at ?? null,
        ] : null;

        $dataResponse = [
            'sessionRecord' => $sessionRecord ? new SessionRecordResource($sessionRecord) : null,
            'attendances' => $this->attendances ? AttendanceResource::collection($this->attendances) : null,
        ];

        return $dataResponse;
    }
}
