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
            'name' => $this->course_name ?? null,
            'grade' => $this->course_grade ?? null,

            'user_id' => $this->pic_course_id ?? null,
            'user_name' => $this->pic_course_name ?? null,
            'user_username' => $this->pic_course_username ?? null,
            'user_role' => $this->pic_course_role ?? null,
            'user_avatar' => $this->pic_course_avatar ?? null,
        ] : null;

        $module = $this->module_id ? (object)[
            'id' => $this->module_id,
            'name' => $this->module_name ?? null,
            'description' => $this->module_description ?? null,
            'file' => $this->module_file ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,

            'course' => $course ? new CourseResource($course) : null,
            'module' => $module ? new ModuleResource($module) : null,

            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
