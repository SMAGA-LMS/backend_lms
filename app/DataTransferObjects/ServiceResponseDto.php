<?php

namespace App\DataTransferObjects;

class ServiceResponseDto
{
    public readonly bool $isSuccess;
    public readonly string $message;
    public readonly mixed $errors;
    public readonly mixed $data;
    public readonly int $codeResponse;

    public function __construct(bool $isSuccess, string $message, $errors, $data, $codeResponse)
    {
        $this->isSuccess = $isSuccess;
        $this->message = $message;
        $this->errors = $errors;
        $this->data = $data;
        $this->codeResponse = $codeResponse;
    }
}
