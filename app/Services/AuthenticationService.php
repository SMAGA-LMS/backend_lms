<?php

namespace App\Services;

use App\dto\AuthDTO;
use App\dto\AuthResponseDTO;
use App\dto\AuthValidationDTO;
use App\Http\Resources\ValidationErrorResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    protected $auth;
    protected $user;

    public function __construct(Auth $auth, User $user)
    {
        $this->auth = $auth;
        $this->user = $user;
    }

    public function validateUserCredentials($credentials)
    {
        if (!$this->auth::attempt($credentials)) {
            return new AuthResponseDTO(
                false,
                [
                    'general' => ['Username or password is incorrect.']
                ],
                null
            );
        }

        $user = $this->auth::user();
        return new AuthResponseDTO(
            true,
            [
                'general' => ['Success Login.']
            ],
            $user
        );
    }

    public function generateToken(User $user)
    {
        return $user->createToken("{$user->username} login")->plainTextToken;
    }

    public function deleteCurrentToken($user)
    {
        $currentToken = $user->currentAccessToken();

        if (!$currentToken) {
            return new AuthResponseDTO(
                false,
                [
                    'general' => ['No active token found']
                ],
                null
            );
        }

        try {
            // ga berhasil pakai raw query karena ga match dengan sanctum
            // $deleteTokenQuery =
            //     'DELETE FROM personal_access_tokens
            //         WHERE token = :currentToken
            //     ';
            // DB::delete(
            //     $deleteTokenQuery,
            //     ['currentToken' => $currentToken]
            // );
            $currentToken->delete();
        } catch (Exception $e) {
            return new AuthResponseDTO(
                false,
                [
                    'general' => ['Failed to logout: database fail to delete the token']
                ],
                null
            );
        }

        return new AuthResponseDTO(
            true,
            [
                'general' => ['User Successfully Logout']
            ],
            null
        );
    }
}
