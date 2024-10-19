<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\ResponseDto;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest\AddNewUserRequest;
use App\Http\Resources\UserResource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
// use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    // pakai query param /users?role=RoleName
    public function index(Request $request)
    {
        $role = $request->query('role');
        if (!isset($role) || $role === '') $result = $this->getAllUserList();
        else $result = $this->getSpecificUserList($role);

        $users = $result->data;
        return $this->apiResponse->successResponse(
            message: $result->message,
            data: UserResource::collection($users),
            codeResponse: $result->codeResponse
        );

        // CHANGE: pindah ke method getAllUserList()
        // //get users
        // $users = User::all();

        // //return collection of users as a resource
        // return new UserResource(true, 'List Data User', $users);
    }

    public function getAllUserList(): ResponseDto
    {

        $users = User::all();

        $message = "List of users retrieved successfully.";
        if (empty($users)) $message = "No users found.";

        return new ResponseDto(
            isSuccess: true,
            message: $message,
            data: $users,
            codeResponse: 200
        );
    }

    public function getSpecificUserList(string $role): ResponseDto
    {
        if (empty($role)) {
            return new ResponseDto(
                isSuccess: true,
                message: "No users found.",
                data: [],
                codeResponse: 200
            );
        }

        $users = DB::table('users')->where('role', $role)->get();

        if ($users->isEmpty()) {
            return new ResponseDto(
                isSuccess: true,
                message: "No " . $role . " found.",
                data: [],
                codeResponse: 200
            );
        }

        return new ResponseDto(
            isSuccess: true,
            message: "List of " . $users->first()->role . " retrieved successfully.",
            data: $users,
            codeResponse: 200
        );
    }

    public function store(AddNewUserRequest $request)
    {
        // CHANGE: pindah ke AddNewUserRequest
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

        $validatedNewUser = $request->validated();


        //upload image
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $image->storeAs('public/UserProfilePicture', $image->hashName());
            $imageDb = $image->hashName();
        } else {
            $imageDb = "null";
        }

        // new username no space and max 16 characters
        $newUsername = substr(str_replace(' ', '', $validatedNewUser['name']), 0, 16);

        //create user
        $users = User::create([
            'name'     => $validatedNewUser['name'],
            'username' => $newUsername,
            'role'     => $validatedNewUser['role'],
            'avatar'   => $imageDb,
            'password' => Hash::make($validatedNewUser['password']),
        ]);

        // $users = DB::insert('insert into users (name, role, avatar, password) values (?, ?, ?, ?)', [$request->name, $request->role, $image->hashName(), Hash::make($request->password)]);

        //return response
        // return new UserResource(true, 'New User added', $users);
        return $this->apiResponse->successResponse(
            message: "New user added.",
            data: new UserResource($users),
            codeResponse: 201
        );
    }

    public function show($id)
    {
        //find post by ID
        $user = User::find($id);

        //return single post as a resource
        if ($user == null) {
            return new UserResource(false, 'User not found', $user);
        } else {
            return new UserResource(true, 'Detail User', $user);
        }
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id'      => 'required',
            'password'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $id = $request->id;
        $user = User::find($id);
        $pw = $request->password;

        // if(Auth::attempt(['id'=>$id, 'password'=>$request->password])){
        //     return new UserResource(true, 'Detail User', $user);
        // }

        if ($user == NULL) {
            return new UserResource(false, 'User not found', $id);
        } else if (!Hash::check($pw, $user->password)) {
            return new UserResource(false, 'Wrong password', $pw);
        } else {
            //return single post as a resource
            return new UserResource(true, 'Logged in', $user);
        }
    }

    // jadi pake yang method index aja, pembedanya dari query param
    // public function adminList()
    // {
    //     //get users
    //     $users = DB::table('users')->where('role', 'Admin')->get();

    //     //return collection of users as a resource
    //     return new UserResource(true, 'List Data User', $users);
    // }

    // public function studentList()
    // {
    //     //get users
    //     $users = DB::table('users')->where('role', 'Student')->get();

    //     if($users == "[]"){
    //         return new UserResource(false, 'No Students found', $users);
    //     }
    //     else{
    //         //return collection of users as a resource
    //         return new UserResource(true, 'List Data Student', $users);
    //     }

    // }

    // public function teacherList()
    // {
    //     //get users
    //     $users = DB::table('users')->where('role', 'Teacher')->get();

    //     if($users == "[]"){
    //         return new UserResource(false, 'No Teachers found', $users);
    //     }
    //     else{
    //         //return collection of users as a resource
    //         return new UserResource(true, 'List Data Teacher', $users);
    //     }

    // }
}
