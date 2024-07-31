<?php

namespace App\DataTransferObjects\Users;

use App\Enums\UserGenderEnum;
use App\Enums\UserRoleEnum;
use Brick\Math\BigInteger;
use Illuminate\Support\Facades\Date;

class UserRequestDto
{
    public readonly string $fullName;
    public readonly int $roleId;
    public readonly string $gender;
    public readonly string $birthDate;

    public function __construct(string $fullName, int $roleId, string $gender, string $birthDate)
    {
        $this->fullName = $fullName;
        $this->roleId = $roleId;
        $this->gender = $gender;
        $this->birthDate = $birthDate;
    }
}
