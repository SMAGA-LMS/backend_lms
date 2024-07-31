<?php

namespace App\DataTransferObjects;

class AuthRequestDto
{
    public function __construct(
        public readonly string $username,
        public readonly string $password
    ) {
    }
}
