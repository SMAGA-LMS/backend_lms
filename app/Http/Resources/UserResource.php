<?php

namespace App\Http\Resources;

use App\Enums\UserRoleEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'user_code' => $this->user_code,
            'username' => $this->username,
            'role' => $this->whenLoaded('role'),
            'full_name' => $this->full_name,
            'avatar' => $this->avatar
        ];

        return $dataResponse;
    }
}
