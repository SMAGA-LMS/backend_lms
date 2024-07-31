<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListClassPeriodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this);
        // 'id' => $this->resource['class_period_id'],
        // 'class_period_code' => $this->resource['class_period_code'],
        // 'grade_level_name' => $this->resource['grade_level_name'],
        // 'classroom_name' => $this->resource['classroom_name'],
        // 'academic_term_name' => $this->resource['academic_term_name'],
        // 'class_period_name' => $this->resource['class_period_name'],
        // 'total_students_enrolled' => $this->resource['total_students_enrolled']
        $dataResponse = [
            'class_period_id' => $this->class_period_id,
            'class_period_code' => $this->class_period_code,
            'grade_level_name' => $this->grade_level_name,
            'classroom_name' => $this->classroom_name,
            'academic_term_name' => $this->academic_term_name,
            'class_period_name' => $this->class_period_name,
            'total_students_enrolled' => $this->total_students_enrolled
        ];

        return $dataResponse;
    }
}
