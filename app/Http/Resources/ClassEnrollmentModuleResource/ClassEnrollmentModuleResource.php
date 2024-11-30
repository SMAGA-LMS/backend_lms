<?php

namespace App\Http\Resources\ClassEnrollmentModuleResource;

use App\Http\Resources\ClassEnrollmentResource\ClassEnrollmentResource;
use App\Http\Resources\ModuleResource\ModuleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassEnrollmentModuleResource extends JsonResource
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

            'user_id' => $this->teachers_id ?? null,
            'user_name' => $this->teachers_name ?? null,
            'user_username' => $this->teachers_username ?? null,
            'user_role' => $this->teachers_role ?? null,
            'user_avatar' => $this->teachers_avatar ?? null,
            'user_created_at' => $this->teachers_created_at ?? null,
            'user_updated_at' => $this->teachers_updated_at ?? null,

            'created_at' => $this->class_enrollment_created_at ?? null,
            'updated_at' => $this->class_enrollment_updated_at ?? null,
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

            'classEnrollment' => $classEnrollment ? new ClassEnrollmentResource($classEnrollment) : null,
            'module' => $module ? new ModuleResource($module) : null,

            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];

        return $dataResponse;
    }
}
