<?php

namespace App\Http\Resources\AuthenticationResource;

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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                'role' => $this->user->role,
                'avatar' => $this->user->avatar
            ],
        ];

        return $dataResponse;
    }
}
