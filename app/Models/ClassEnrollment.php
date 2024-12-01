<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassEnrollment extends Model
{
    use HasFactory;

    public $table = 'class_enrollments';

    protected $fillable = [
        'course_id',
        'classroom_id',
        'user_id',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getClassEnrollmentsByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->leftJoin('classrooms', 'class_enrollments.classroom_id', '=', 'classrooms.id')
            ->leftJoin('courses', 'class_enrollments.course_id', '=', 'courses.id')
            ->leftJoin('users', 'class_enrollments.user_id', '=', 'users.id')
            ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            ->select(
                'class_enrollments.*',

                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade',
                'classrooms.created_at as classroom_created_at',
                'classrooms.updated_at as classroom_updated_at',

                'courses.name as course_name',
                'courses.user_id as course_user_id',
                'courses.grade as course_grade',
                'courses.created_at as course_created_at',
                'courses.updated_at as course_updated_at',

                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar',
                'users.created_at as user_created_at',
                'users.updated_at as user_updated_at',

                'pic_courses.id as pic_course_id',
                'pic_courses.name as pic_course_name',
                'pic_courses.username as pic_course_username',
                'pic_courses.role as pic_course_role',
                'pic_courses.avatar as pic_course_avatar',
                'pic_courses.created_at as pic_course_created_at',
                'pic_courses.updated_at as pic_course_updated_at',
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

    public function getClassEnrollmentByID($id)
    {
        return DB::table($this->table)
            ->leftJoin('classrooms', 'class_enrollments.classroom_id', '=', 'classrooms.id')
            ->leftJoin('courses', 'class_enrollments.course_id', '=', 'courses.id')
            ->leftJoin('users', 'class_enrollments.user_id', '=', 'users.id')
            ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            ->select(
                'class_enrollments.*',

                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade',
                'classrooms.created_at as classroom_created_at',
                'classrooms.updated_at as classroom_updated_at',

                'courses.name as course_name',
                'courses.user_id as course_user_id',
                'courses.grade as course_grade',
                'courses.created_at as course_created_at',
                'courses.updated_at as course_updated_at',

                'users.name as user_name',
                'users.username as user_username',
                'users.role as user_role',
                'users.avatar as user_avatar',
                'users.created_at as user_created_at',
                'users.updated_at as user_updated_at',

                'pic_courses.id as pic_course_id',
                'pic_courses.name as pic_course_name',
                'pic_courses.username as pic_course_username',
                'pic_courses.role as pic_course_role',
                'pic_courses.avatar as pic_course_avatar',
                'pic_courses.created_at as pic_course_created_at',
                'pic_courses.updated_at as pic_course_updated_at',
            )
            ->where('class_enrollments.id', $id)
            ->first();
    }

    public function insertNewClassEnrollment(array $data)
    {
        $data['created_at'] = now();
        return DB::table($this->table)->insertGetId($data);
    }

    public function updateClassEnrollment($id, array $data)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update($data);
    }

    // butuh join ke course, dan classroom
    public function getStudentClassEnrollment($userID)
    {
        return DB::table($this->table)
            ->join('student_enrollments', 'class_enrollments.classroom_id', '=', 'student_enrollments.classroom_id')
            ->leftJoin('classrooms', 'class_enrollments.classroom_id', '=', 'classrooms.id')
            ->leftJoin('courses', 'class_enrollments.course_id', '=', 'courses.id')
            ->select(
                'class_enrollments.*',

                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade',
                'classrooms.created_at as classroom_created_at',
                'classrooms.updated_at as classroom_updated_at',

                'courses.name as course_name',
                'courses.user_id as course_user_id',
                'courses.grade as course_grade',
                'courses.created_at as course_created_at',
                'courses.updated_at as course_updated_at',
            )
            ->where('student_enrollments.user_id', $userID)
            ->get();
    }
}
