<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\Users\UserRequestDto;
use App\Enums\UserGenderEnum;
use App\Enums\UserRoleEnum;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddNewUserRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Services\UserService;
use Brick\Math\BigInteger;
use Illuminate\Support\Facades\Date;

class UserController
{
    protected $userService;
    protected $apiResponse;

    public function __construct(UserService $userService, ApiResponseHelper $apiResponse)
    {
        $this->userService = $userService;
        $this->apiResponse = $apiResponse;
    }

    //
    // public function index()
    // {
    //     //get users
    //     $users = User::all();

    //     //return collection of users as a resource
    //     return new UserResource(true, 'List Data User', $users);
    // }

    public function addNewUser(AddNewUserRequest $request)
    {
        $newUserDto = new UserRequestDto(
            fullName: $request->validated('full_name'),
            roleId: $request->validated('role_id'),
            gender: $request->validated('gender'),
            birthDate: $request->validated('birth_date')
        );

        $result = $this->userService->addNewUser($newUserDto);

        // if (!$result->isSuccess) {

        // }

        return $this->apiResponse->successResponse(
            message: $result->message,
            data: new UserResource($result->data),
            codeResponse: $result->codeResponse
        );


        // //define validation rules
        // $validator = Validator::make($request->all(), [
        //     'name'      => 'required',
        //     'role'      => ['required', Rule::in(['Admin', 'Student', 'Teacher', 'Testing']),],
        //     'avatar'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        //     'password'     => 'required',
        // ]);

        // //check if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        // //upload image
        // if ($request->hasFile('avatar')) {
        //     $image = $request->file('avatar');
        //     $image->storeAs('public/UserProfilePicture', $image->hashName());
        //     $imageDb = $image->hashName();
        // } else {
        //     $imageDb = "null";
        // }


        // //create user
        // $users = User::create([
        //     'name'     => $request->name,
        //     'role'   => $request->role,
        //     'avatar'     => $imageDb,
        //     'password' => Hash::make($request->password)
        // ]);
        // // $users = DB::insert('insert into users (name, role, avatar, password) values (?, ?, ?, ?)', [$request->name, $request->role, $image->hashName(), Hash::make($request->password)]);

        // //return response
        // return new UserResource(true, 'New User added', $users);
    }

    // public function show($id)
    // {
    //     //find post by ID
    //     $user = User::find($id);

    //     //return single post as a resource
    //     if($user==null){
    //         return new UserResource(false, 'User not found', $user);
    //     }
    //     else{
    //         return new UserResource(true, 'Detail User', $user);
    //     }

    // }

    public function getUserList(Request $request)
    {
        $role_id = $request->query('role_id');

        if (!isset($role_id) || $role_id === '') $result = $this->userService->getAllUserList();
        else $result = $this->userService->getSpecificUserList($role_id);

        $users = $result->data;
        return $this->apiResponse->successResponse(
            message: $result->message,
            data: UserResource::collection($users),
            codeResponse: $result->codeResponse
        );
    }
}
