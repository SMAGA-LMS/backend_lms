<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\ResponseDto;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthenticationResource\LoginResource;
use App\Http\Resources\TokenAuthResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function login(LoginRequest $request)
    {
        $deviceName = $request->input('deviceName');
        if (empty($deviceName)) $deviceName = "can't detect device";

        $credentials = $request->validated();

        // CHANGE: pindah ke LoginRequest rules()
        // $validator = Validator::make($request->all(), [
        //     'id'      => 'required',
        //     'password'     => 'required',
        // ]);

        // CHANGE: pindah ke LoginRequest failedValidation() maupun failedAuthorization()
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        $result = $this->validateUserCredentials($credentials);

        if (!$result->isSuccess) {
            return $this->apiResponse->errorResponse(
                message: $result->message,
                errors: $result->errors,
                codeResponse: $result->codeResponse
            );
        }

        $user = $result->data;
        $token = $this->generateToken($user, $deviceName);

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: new LoginResource($user, $token),
            codeResponse: $result->codeResponse
        );

        // if (!Auth::attempt($credentials)) {
        //     return new UserResource(false, 'Unauthorized', $credentials);
        // }

        // udah dilakuin di validateUserCredentials(), tapi yang user not found ga perlu (ini sama aja kayak leaked data, biarin aja wrong username or password)
        // //find post by ID
        // $id = $request->id;
        // $user = User::find($id);
        // $pw = $request->password;

        // // if(Auth::attempt(['id'=>$id, 'password'=>$request->password])){
        // //     return new UserResource(true, 'Detail User', $user);
        // // }

        // if ($user == NULL) {
        //     return new UserResource(false, 'User not found', $id);
        // } else if (!Hash::check($pw, $user->password)) {
        //     return new UserResource(false, 'Wrong password', $pw);
        // } else {
        //     //return single post as a resource
        //     return new UserResource(true, 'Logged in', $user);
        // }
    }

    private function validateUserCredentials($credentials): ResponseDto
    {
        if (!Auth::attempt($credentials)) {
            return new ResponseDto(
                isSuccess: false,
                message: "Authentication failed",
                errors: [
                    'Username or password is incorrect'
                ],
                data: null,
                codeResponse: 401
            );
        }

        $user = Auth::user();
        return new ResponseDto(
            isSuccess: true,
            message: 'Success Login',
            errors: [],
            data: $user,
            codeResponse: 200
        );
    }

    private function generateToken(User $user, $deviceName)
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $result = $this->deleteCurrentToken($user);

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

    private function deleteCurrentToken($user): ResponseDto
    {
        $currentToken = $user->currentAccessToken();

        if (!$currentToken) {
            return new ResponseDto(
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
            return new ResponseDto(
                isSuccess: false,
                message: "Failed to logout",
                errors: [
                    "Database fail to delete the token."
                ],
                data: null,
                codeResponse: 500
            );
        }

        return new ResponseDto(
            isSuccess: true,
            message: "Successfully Logout",
            errors: [],
            data: [],
            codeResponse: 200
        );
    }
}
