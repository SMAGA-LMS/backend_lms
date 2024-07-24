<?php

namespace App\dto;

class AuthResponseDTO
{
    public $isSuccess;
    public $message;
    public $data;

    public function __construct(bool $isSuccess, $message, $data)
    {
        $this->isSuccess = $isSuccess;
        $this->message = $message;
        $this->data = $data;
    }
}
