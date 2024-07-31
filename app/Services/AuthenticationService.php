<?php

namespace App\Services;

use App\DataTransferObjects\AuthRequestDto;
use App\DataTransferObjects\AuthResponseDTO;
use App\DataTransferObjects\ServiceResponseDto;
use App\DataTransferObjects\UserDto;
use App\dto\AuthDTO;
use App\dto\AuthValidationDTO;
use App\Http\Requests\ValidateAuthUserRequest;
use App\Http\Resources\ValidationErrorResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;

class AuthenticationService
{

    public function validateUserCredentials($credentials): ServiceResponseDto
    {
        // $credentials = [
        //     'username' => $authRequestDto->username,
        //     'password' => $authRequestDto->password,
        // ];

        if (!Auth::attempt($credentials)) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Authentication failed.",
                errors: [
                    'Username or password is incorrect.'
                ],
                data: null,
                codeResponse: 401
            );
        }

        $user = Auth::user();
        return new ServiceResponseDto(
            isSuccess: true,
            message: 'Success Login.',
            errors: [],
            data: $user,
            codeResponse: 200
        );
    }

    public function generateToken(User $user, $deviceName)
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    public function deleteCurrentToken($user): ServiceResponseDto
    {
        $currentToken = $user->currentAccessToken();

        if (!$currentToken) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Logout failed.",
                errors: [
                    'Token not found.'
                ],
                data: null,
                codeResponse: 401
            );
        }

        try {
            $currentToken->delete();
        } catch (Exception $e) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Failed to logout",
                errors: [
                    "Database fail to delete the token."
                ],
                data: null,
                codeResponse: 500
            );
        }

        return new ServiceResponseDto(
            isSuccess: true,
            message: "User Successfully Logout",
            errors: [],
            data: [],
            codeResponse: 200
        );
    }

    public function getCurrentUser(User $user): ServiceResponseDto
    {
        // pakai ::selectOne biar return nya lansung satu object
        // kalau pakai ::select dia return nya array object, jadi mesti pakai [0]
        // $data = DB::selectOne(
        //     'SELECT
        //         u.id,
        //         u.username,
        //         r.name AS role_name,
        //         s.student_code AS student_code,
        //         t.teacher_code AS teacher_code
        //         u.full_name,
        //         u.avatar,
        //     FROM users AS u
        //     WHERE username = :username
        //     JOIN roles AS r ON u.role_id = r.id
        //     LEFT JOIN students AS s ON u.id = s.user_id
        //     LEFT JOIN teachers AS t ON u.id = t.user_id
        //     LIMIT 1
        //     ',
        //     [
        //         'username' => $credentials['username']
        //     ]
        // );

        // kalau pakai eloquent relasi gini, pake with
        // role:id,name artiya nanti yang diambil cuma id, dan name aja.
        // Jangan pake spasi (role:id, name) -> ini error, harus (role:id, name)
        // harus pakai primary_key juga, kalau (role:name) -> error akan null, jadi harus ada primary_key nya (role:id,column_lain)
        $user = User::with(['role:id,name'])
            ->where('username', $user->username)
            ->first();

        if (!$user) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Failed to get current user.",
                errors: [
                    "User not found."
                ],
                data: null,
                codeResponse: 404
            );
        }

        return new ServiceResponseDto(
            isSuccess: true,
            message: "Success get current user.",
            errors: [],
            data: $user,
            codeResponse: 200
        );
    }
}
