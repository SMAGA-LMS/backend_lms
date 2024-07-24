<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthenticationResource extends JsonResource
{
    public $isSuccess;
    public $successMessage;

    public function __construct(bool $isSuccess, $successMessage, $resource)
    {
        parent::__construct($resource);
        $this->isSuccess = $isSuccess;
        $this->successMessage = $successMessage;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'isSuccess' => $this->isSuccess,
            'message' => $this->successMessage,
            'data' => [
                'token' => $this->resource
            ]
        ];
    }
}
