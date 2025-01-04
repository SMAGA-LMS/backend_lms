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
            'created_at' => $this->course_created_at ?? null,
            'updated_at' => $this->course_updated_at ?? null,

            'user_id' => $this->pic_course_id ?? null,
            'user_name' => $this->pic_course_name ?? null,
            'user_username' => $this->pic_course_username ?? null,
            'user_role' => $this->pic_course_role ?? null,
            'user_avatar' => $this->pic_course_avatar ?? null,
            'user_created_at' => $this->pic_course_created_at ?? null,
            'user_updated_at' => $this->pic_course_updated_at ?? null,
        ] : null;

        $module = $this->module_id ? (object)[
            'id' => $this->module_id,
            'name' => $this->module_name ?? null,
            'description' => $this->module_description ?? null,
            'file' => $this->module_file ?? null,
            'created_at' => $this->module_created_at ?? null,
            'updated_at' => $this->module_updated_at ?? null,
        ] : null;

        $dataResponse = [
            'id' => $this->id,

            'course' => $course ? new CourseResource($course) : null,
            'module' => $module ? new ModuleResource($module) : null,

            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
