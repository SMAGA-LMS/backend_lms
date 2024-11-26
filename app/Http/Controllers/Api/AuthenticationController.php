<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticationRequest\LoginRequest;
use App\Http\Resources\AuthenticationResource\AuthMeResource;
use App\Http\Resources\AuthenticationResource\LoginResource;
use App\Http\Resources\TokenAuthResource;
use App\Http\Resources\UserResource;
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

    // LMS-71
    public function login(LoginRequest $request)
    {
        $deviceName = $request->input('device_name');
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

        $user = $this->validateUserCredentials($credentials);

        // if (!$result->isSuccess) {
        //     return $this->apiResponse->errorResponse(
        //         message: $result->message,
        //         errors: $result->errors,
        //         codeResponse: $result->codeResponse
        //     );
        // }

        // $user = $result->data;
        $token = $this->generateToken($user, $deviceName);

        // return $this->apiResponse->successResponse(
        //     message: $result->message,
        //     data: new LoginResource($user, $token),
        //     codeResponse: $result->codeResponse
        // );
        return $this->apiResponse->successResponse(
            message: "Success Login",
            data: new LoginResource($user, $token),
            codeResponse: 200
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

    // bagian dari LMS-71
    private function validateUserCredentials($credentials)
    {
        if (!Auth::attempt($credentials)) {
            return $this->apiResponse->errorResponse(
                message: "Authentication failed",
                errors: [
                    'Username or password is incorrect'
                ],
                codeResponse: 401
            );
        }

        $user = Auth::user();

        return $user;
    }

    // bagian dari LMS-71
    private function generateToken(User $user, $deviceName)
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    // LMS-64
    public function logout(Request $request)
    {
        $user = $request->user();
        return $this->deleteCurrentToken($user);
    }

    // bagian dari LMS-64
    private function deleteCurrentToken($user)
    {
        $currentToken = $user->currentAccessToken();

        if (!$currentToken) {
            return $this->apiResponse->errorResponse(
                message: "Logout failed.",
                errors: [
                    'Token not found.'
                ],
                codeResponse: 401
            );
        }

        try {
            $currentToken->delete();
        } catch (Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to logout",
                errors: [
                    "Database fail to delete the token."
                ],
                codeResponse: 500
            );
        }

        return $this->apiResponse->successResponse(
            message: "Successfully Logout",
            data: [],
            codeResponse: 200
        );
    }

    // LMS-66
    public function authMe(Request $request)
    {
        try {
            $user = $request->user();
        } catch (Exception $e) {
            return $this->apiResponse->errorResponse(
                message: "Failed to get current user.",
                errors: [
                    $e->getMessage()
                ],
                codeResponse: 404
            );
        }

        return $this->apiResponse->successResponse(
            message: "Success get current user",
            data: new AuthMeResource($user),
            codeResponse: 200
        );
    }
}
