<?php

namespace App\Http\Resources\CourseResource;

use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'user' => $this->user ? new UserResource($this->user) : null,
            // kalau pakai eloquent kayak di bawah ini
            // 'user' => new UserResource($this->whenLoaded('user')),
        ];

        return $dataResponse;
    }
}
