<?php

namespace App\Http\Resources\CourseModuleResource;

use App\Http\Resources\CourseResource\CourseResource;
use App\Http\Resources\ModuleResource\ModuleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleResource extends JsonResource
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

        $module = $this->module_id ? (object)[
            'id' => $this->module_id,
            'name' => $this->module_name,
            'description' => $this->module_description,
            'file' => $this->module_file,
        ] : null;

        $dataResponse = [
            'id' => $this->id,
            'course' => $course ? new CourseResource($course) : null,
            'module' => $module ? new ModuleResource($module) : null,
        ];

        return $dataResponse;
    }
}
