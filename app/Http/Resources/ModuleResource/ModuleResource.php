<?php

namespace App\Http\Resources\ModuleResource;

use App\Http\Resources\CourseResource\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $course = $this->course_id ? (object)[
            'id' => $this->course_id,
            'name' => $this->course_name,
            'grade' => $this->course_grade,

            'user_id' => $this->pic_course_id,
            'user_name' => $this->pic_course_name,
            'user_username' => $this->pic_course_username,
            'user_role' => $this->pic_course_role,
            'user_avatar' => $this->pic_course_avatar,
        ] : null;

        $dataResponse = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'course' => $this->course_id ? new CourseResource($course) : null,
            'file' => $this->file ? url('storage/Modules/' . $this->file) : null,
        ];

        return $dataResponse;
    }
}
