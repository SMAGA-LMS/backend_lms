<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailClassPeriodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $teacher = null;
        if ($this->teacher_full_name !== null) {
            $teacher = [
                'user_code' => $this->teacher_code,
                'full_name' => $this->teacher_full_name,
                'avatar' => $this->avatar_teacher,
            ];
        }

        return [
            'class_period' => [
                'id' => $this->class_period_id,
                'name' => $this->class_period_name,
                'code' => $this->class_period_code,
            ],
            'teacher' => $teacher
        ];
    }
}
