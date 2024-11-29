<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentEnrollment extends Model
{
    use HasFactory;

    public $table = 'student_enrollments';

    protected $fillable = [
        'user_id',
        'classroom_id',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getStudentEnrollmentsByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->leftJoin('users', 'users.id', '=', 'student_enrollments.user_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'student_enrollments.classroom_id')
            ->select(
                'student_enrollments.*',

                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar',
                'users.created_at as user_created_at',
                'users.updated_at as user_updated_at',

                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade',
                'classrooms.created_at as classroom_created_at',
                'classrooms.updated_at as classroom_updated_at',
            );

        foreach ($conditions as $field => $value) {
            $field = $this->table . '.' . $field;
            if (is_array($value)) {
                $query->where($field, $value[self::OPERATOR], $value[self::VALUE_FIELD]);
            } else {
                $query->where($field, $value);
            }
        }

        return $isCollection ? $query->get() : $query->first();
    }

    public function getEnrolledUser($userID, $classroomID)
    {
        return DB::table($this->table)
            ->where('user_id', $userID)
            ->where('classroom_id', $classroomID)
            ->first();
    }

    public function insertNewStudentEnrollment(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function getStudentEnrollmentByID($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }
}
