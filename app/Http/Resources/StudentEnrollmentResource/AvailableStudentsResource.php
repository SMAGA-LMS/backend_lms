<?php

namespace App\Http\Resources\StudentEnrollmentResource;

use App\Http\Resources\ClassroomResource\ClassroomResource;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableStudentsResource extends JsonResource
{
    private $classroom;
    private $users;

    public function __construct($classroom, $users)
    {
        $this->classroom = $classroom;
        $this->users = $users;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $classroom = $this->classroom->id ? (object)[
            'id' => $this->classroom->id,
            'name' => $this->classroom->name ?? null,
            'grade' => $this->classroom->grade ?? null,
            'created_at' => $this->classroom->created_at ?? null,
            'updated_at' => $this->classroom->updated_at ?? null,
        ] : null;

        $responseData = [
            'classroom' => $classroom ? new ClassroomResource($classroom) : null,
            'users' => $this->users ? UserResource::collection($this->users) : null,
        ];

        return $responseData;
    }
}
