<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthenticationResource;
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

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // validasi kredensial user di AuthenticationService
        $result = $this->authService->validateUserCredentials($credentials);
        $isSuccess = $result->isSuccess;

        if (!$isSuccess) {
            $errorsMessage = $result->message;
            return (
                new ValidationErrorResource($isSuccess, $errorsMessage)
            )->response()->setStatusCode(422);
        }

        $user = $result->data;
        $successMessage = $result->message;
        $token = $this->authService->generateToken($user);

        return (
            new AuthenticationResource(
                $isSuccess,
                $successMessage,
                $token
            )
        )->response()->setStatusCode(201);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $result = $this->authService->deleteCurrentToken($user);
        $isSuccess = $result->isSuccess;

        // jika gagal, bisa karena kegagalan pada database, dsb (check di AuthenticationService)
        if (!$isSuccess) {
            $errorMessage = $result->message;
            return (
                new ValidationErrorResource($isSuccess, $errorMessage)
            )->response()->setStatusCode(422);
        }

        $successMessage = $result->message;
        $token = null;
        return response()->json()->setStatusCode(204);
    }
}
