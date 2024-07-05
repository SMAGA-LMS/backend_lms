<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function index()
    {
        //get users
        $users = User::latest()->paginate(5);

        //return collection of users as a resource
        return new UserResource(true, 'List Data User', $users);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'role'      => ['required', Rule::in(['Admin', 'Student', 'Teacher', 'Testing']),],
            'avatar'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        if($request->hasFile('avatar')){
            $image = $request->file('avatar');
            $image->storeAs('public/UserProfilePicture', $image->hashName());
            $imageDb = $image->hashName();
        }
        else{
            $imageDb = "null";
        }


        //create user
        $users = User::create([
            'name'     => $request->name,
            'role'   => $request->role,
            'avatar'     => $imageDb,
            'password' => Hash::make($request->password)
        ]);
        // $users = DB::insert('insert into users (name, role, avatar, password) values (?, ?, ?, ?)', [$request->name, $request->role, $image->hashName(), Hash::make($request->password)]);

        //return response
        return new UserResource(true, 'New User added', $users);
    }
}
