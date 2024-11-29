<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // HasApiTokens untuk create token, dan urusan token lainnya
    use HasApiTokens, HasFactory, Notifiable;

    public $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'role',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = [
    //     'password',
    //     // 'remember_token',
    // ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    //     protected function casts(): array
    //     {
    //         return [
    //             'email_verified_at' => 'datetime',
    //             'password' => 'hashed',
    //         ];
    //     }

    public function generateToken(User $user, $deviceName)
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getUsersByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table);

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->where($field, $value[self::OPERATOR], $value[self::VALUE_FIELD]);
            } else {
                $query->where($field, $value);
            }
        }

        return $isCollection ? $query->get() : $query->first();
    }

    public function insertNewUser(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function getUserByID($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }

    public function getAvailableStudents($listOfEnrolledUsersID)
    {
        return DB::table($this->table)
            ->whereNotIn('id', $listOfEnrolledUsersID)
            ->where('role', 'STUDENT')
            ->get();
    }
}
