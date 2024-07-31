<?php

namespace App\DataTransferObjects;

class AuthResponseDTO
{
    public $isSuccess;
    public $message;
    public $errors;
    public $data;

    public function __construct(bool $isSuccess, string $message, $errors, $data)
    {
        $this->isSuccess = $isSuccess;
        $this->message = $message;
        $this->errors = $errors;
        $this->data = $data;
    }
}
