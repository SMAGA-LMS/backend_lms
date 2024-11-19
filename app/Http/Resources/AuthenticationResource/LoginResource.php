<?php

namespace App\Http\Resources\AuthenticationResource;

use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    protected $token;
    protected $user;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
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
            'token' => $this->token
        ];

        return $dataResponse;
    }
}
