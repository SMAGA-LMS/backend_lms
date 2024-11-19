<?php

namespace App\Http\Resources\AuthenticationResource;

use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthMeResource extends JsonResource
{

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataResponse = [
            'user' => $this->user ? new UserResource($this->user) : null,
        ];

        return $dataResponse;
    }
}
