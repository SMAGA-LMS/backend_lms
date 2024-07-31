<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponseDto;
use App\Models\Role;

class RoleService
{
    public function getAllRoles(): ServiceResponseDto
    {
        $roles = Role::all();

        return new ServiceResponseDto(
            isSuccess: true,
            message: "Success get all available roles",
            errors: [],
            data: $roles,
            codeResponse: 200
        );
    }
}
