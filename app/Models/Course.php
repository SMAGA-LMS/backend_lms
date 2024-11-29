<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use HasFactory;

    public $table = 'courses';

    protected $fillable = [
        'name',
        'user_id',
        'grade',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getCoursesByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->leftJoin('users', 'users.id', '=', 'courses.user_id')
            ->select(
                'courses.*',

                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar',
                'users.created_at as user_created_at',
                'users.updated_at as user_updated_at',
            );

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->where($field, $value[self::OPERATOR], $value[self::VALUE_FIELD]);
            } else {
                $query->where($field, $value);
            }
        }

        return $isCollection ? $query->get() : $query->first();
    }

    public function getCourseByID($id)
    {
        return DB::table($this->table)
            ->leftJoin('users', 'users.id', '=', 'courses.user_id')
            ->select(
                'courses.*',

                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar',
                'users.created_at as user_created_at',
                'users.updated_at as user_updated_at',
            )
            ->where('courses.id', $id)
            ->first();
    }

    public function insertNewCourse(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function updateCourse($id, array $data)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update($data);
    }
}
