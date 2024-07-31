<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponseDto;
use App\DataTransferObjects\Users\UserRequestDto;
use App\Enums\UserGenderEnum;
use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Http\Requests\AddNewUserRequest;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Utils\UserUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;

class UserService
{
    protected $userUtil;
    /**
     * Create a new class instance.
     */
    public function __construct(UserUtil $userUtil)
    {
        $this->userUtil = $userUtil;
    }

    public function getSpecificUserList($role_id): ServiceResponseDto
    {

        $role = DB::selectOne(
            'SELECT *
            FROM roles
            WHERE id = :role_id',
            [
                'role_id' => $role_id
            ]
        );

        if (empty($role)) {
            return new ServiceResponseDto(
                isSuccess: true,
                message: "No users found.",
                errors: [],
                data: [],
                codeResponse: 200
            );
        }

        $users = User::with('role:id,name')
            ->where('role_id', $role_id)
            ->get();

        $message = "List of " . $role->name . " retrieved successfully.";
        if ($users->isEmpty()) $message = "No " . $role->name . " found.";

        return new ServiceResponseDto(
            isSuccess: true,
            message: $message,
            errors: [],
            data: $users,
            codeResponse: 200
        );
    }

    public function getAllUserList(): ServiceResponseDto
    {
        $users = User::with('role:id,name')->get();

        $message = "List of users retrieved successfully.";
        if (empty($users)) $message = "No users found.";

        return new ServiceResponseDto(
            isSuccess: true,
            message: $message,
            errors: [],
            data: $users,
            codeResponse: 200
        );
    }

    public function addNewUser(UserRequestDto $userRequestDto): ServiceResponseDto
    {

        $baseUsername = $this->userUtil->generateUsername($userRequestDto->fullName);
        $user_code = $this->userUtil->generateUserCode($userRequestDto->roleId, $userRequestDto->gender);
        $username = $this->userUtil->ensureUniqueUsername($baseUsername);

        try {

            DB::insert(
                'INSERT INTO users (role_id, status_id, user_code, username, password, full_name, gender, birth_date, created_at)
                VALUES (:role_id, :status_id, :user_code, :username, :password, :full_name, :gender, :birth_date, :created_at)',
                [
                    'role_id' => $userRequestDto->roleId,
                    'status_id' => UserStatusEnum::ACTIVE,
                    'user_code' => $user_code,
                    'username' => $username,
                    'password' => Hash::make($username),
                    'full_name' => $userRequestDto->fullName,
                    'gender' => $userRequestDto->gender,
                    'birth_date' => $userRequestDto->birthDate,
                    'created_at' => Carbon::now()
                ]
            );

            // dd(false);


            // dd($username);

            $newUser = User::where('username', $username)->first();
            // $newUser = DB::selectOne(
            //     'SELECT id
            //     FROM users
            //     WHERE username = :username',
            //     [
            //         'username' => $username
            //     ]
            // );

            // dd($newUser);


            // dd($newUser);
            if ($newUser->role_id === UserRoleEnum::TEACHER) {
                DB::insert(
                    'INSERT INTO teachers (user_id, created_at)
                    VALUES (:user_id, :created_at)',
                    [
                        'user_id' => $newUser->id,
                        'created_at' => Carbon::now()
                    ]
                );
            } elseif ($newUser->role_id === UserRoleEnum::ADMIN) {
                DB::insert(
                    'INSERT INTO admins (user_id, created_at)
                    VALUES (:user_id, :created_at)',
                    [
                        'user_id' => $newUser->id,
                        'created_at' => Carbon::now()
                    ]
                );
            } else {
                DB::insert(
                    'INSERT INTO students (user_id, created_at)
                    VALUES (:user_id, :created_at)',
                    [
                        'user_id' => $newUser->id,
                        'created_at' => Carbon::now()
                    ]
                );
            }

            // dd($newUser);

        } catch (Exception $e) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Failed to add user.",
                errors: [$e->getMessage()],
                data: null,
                codeResponse: 500
            );
        }

        return new ServiceResponseDto(
            isSuccess: true,
            message: "User added successfully.",
            errors: [],
            data: $newUser,
            codeResponse: 201
        );
    }
}
