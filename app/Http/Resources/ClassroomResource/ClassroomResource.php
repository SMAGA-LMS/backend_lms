<?php

namespace App\Http\Resources\ClassroomResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataResponse = [
            'id' => $this->id,
            'name' => $this->name,
            'grade' => $this->grade,
        ];

        return $dataResponse;
    }
}
