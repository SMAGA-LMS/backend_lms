<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValidationErrorResource extends JsonResource
{
    public $isSuccess;
    public $errorMessage;

    public function __construct(bool $isSuccess, $errorMessage, $resource = null)
    {
        parent::__construct($resource);
        $this->isSuccess = $isSuccess;
        $this->errorMessage = $errorMessage;
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
            'message' => $this->errorMessage,
            'data' => $this->resource,
        ];
    }
}
