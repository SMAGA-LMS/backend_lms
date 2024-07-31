<?php

namespace App\Utils;

use App\Enums\UserGenderEnum;
use App\Enums\UserRoleEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserUtil
{
    public function generateUsername(string $fullName): string
    {
        $username = str_replace(' ', '', strtolower($fullName));
        return substr($username, 0, 12); // Truncate to 12 characters
    }

    public function generateUserCode(int $roleId, string $gender): string
    {
        $prefix = null;
        if ($roleId === UserRoleEnum::TEACHER) {
            $prefix = $gender === UserGenderEnum::MALE ? 'TM' : 'TF';
        } elseif ($roleId === UserRoleEnum::ADMIN) {
            $prefix = $gender === UserGenderEnum::MALE ? 'AM' : 'AF';
        } elseif ($roleId === UserRoleEnum::STUDENT) {
            $currentYear = Carbon::now()->format('y');
            return $currentYear . (User::where('role_id', UserRoleEnum::STUDENT)->count() + 1);
        }

        if ($prefix) {
            $latestUserCode = User::where('user_code', 'like', $prefix . '%')
                ->orderBy('user_code', 'desc')
                ->value('user_code');
            $latestNumber = $latestUserCode ? intval(substr($latestUserCode, 2)) : 0;
            return $prefix . ($latestNumber + 1);
        }

        return null;
    }

    public function ensureUniqueUsername(string $baseUsername): string
    {
        $isExit = false;
        $increment = 1;
        $username = $baseUsername;
        do {
            $existUser = DB::selectOne(
                'SELECT username
                FROM users
                WHERE username = :username',
                [
                    'username' => $username
                ]
            );
            if (!empty($existUser)) {
                $username = $baseUsername . $increment;
                $increment++;
            } else {
                $isExit = true;
            }
        } while (!$isExit);

        return $username;
    }
}
