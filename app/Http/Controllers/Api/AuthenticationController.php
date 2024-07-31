<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\AuthRequestDto;
use App\DataTransferObjects\UserDto;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Resources\AuthenticationResource;
use App\Http\Resources\TokenAuthResource;
use App\Http\Resources\UserAuthResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ValidationErrorResource;
use App\Models\User;
use App\Services\AuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    protected $authService;
    protected $apiResponse;

    public function __construct(AuthenticationService $authService, ApiResponseHelper $apiResponse)
    {
        $this->authService = $authService;
        $this->apiResponse = $apiResponse;
    }

    public function login(LoginRequest $request)
    {
        $deviceName = $request->input('device_name');
        if (empty($deviceName)) $deviceName = "can't detect device";

        // $authRequestDto = new AuthRequestDto(
        //     username: $request->validated('username'),
        //     password: $request->validated('password')
        // );

        $credentials = $request->validated();

        // validasi kredensial user di AuthenticationService
        $result = $this->authService->validateUserCredentials($credentials);

        if (!$result->isSuccess) {
            return $this->apiResponse->errorResponse(
                message: $result->message,
                errors: $result->errors,
                codeResponse: $result->codeResponse
            );
        }

        $user = $result->data;
        $token = $this->authService->generateToken($user, $deviceName);

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: new TokenAuthResource($token),
            codeResponse: $result->codeResponse
        );
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $result = $this->authService->deleteCurrentToken($user);

        // jika gagal, bisa karena kegagalan pada database, dsb (check di AuthenticationService)
        if (!$result->isSuccess) {
            return $this->apiResponse->errorResponse(
                message: $result->message,
                errors: $result->errors,
                codeResponse: $result->codeResponse
            );
        }

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: $result->data,
            codeResponse: $result->codeResponse
        );
    }

    public function authMe(Request $request)
    {
        $user = $request->user();

        $result = $this->authService->getCurrentUser($user);

        if (!$result->isSuccess) {
            return $this->apiResponse->errorResponse(
                message: $result->message,
                errors: $result->errors,
                codeResponse: $result->codeResponse
            );
        }

        $data = $result->data;
        // kalau pakai eloquent laravel (ORM), tolong pakai eager loading di resource nya (kalau misal ada relasi table pakai with)
        // kalau ga pakai eager loading ntar tetep kena return walau meski ga pakai with pada saat ambil dari database di service
        return $this->apiResponse->successResponse(
            message: $result->message,
            data: new UserResource($data),
            codeResponse: $result->codeResponse
        );
    }
}
