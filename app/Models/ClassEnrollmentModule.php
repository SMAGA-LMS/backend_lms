<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassEnrollmentModule extends Model
{
    use HasFactory;

    public $table = 'class_enrollment_modules';

    protected $fillable = [
        'class_enrollment_id',
        'module_id',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getClassEnrollmentModulesByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->join('modules', 'class_enrollment_modules.module_id', '=', 'modules.id')
            ->join('class_enrollments', 'class_enrollment_modules.class_enrollment_id', '=', 'class_enrollments.id')
            ->leftJoin('classrooms', 'class_enrollments.classroom_id', '=', 'classrooms.id')
            ->leftJoin('courses', 'class_enrollments.course_id', '=', 'courses.id')
            ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            ->leftJoin('users as teachers', 'class_enrollments.user_id', '=', 'teachers.id')
            ->select(
                'class_enrollment_modules.*',

                'modules.name as module_name',
                'modules.description as module_description',
                'modules.file as module_file',
                'modules.created_at as module_created_at',
                'modules.updated_at as module_updated_at',

                'class_enrollments.created_at as class_enrollment_created_at',
                'class_enrollments.updated_at as class_enrollment_updated_at',

                'classrooms.id as classroom_id',
                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade',
                'classrooms.created_at as classroom_created_at',
                'classrooms.updated_at as classroom_updated_at',

                'courses.id as course_id',
                'courses.name as course_name',
                'courses.grade as course_grade',
                'courses.created_at as course_created_at',
                'courses.updated_at as course_updated_at',

                'pic_courses.id as pic_course_id',
                'pic_courses.name as pic_course_name',
                'pic_courses.username as pic_course_username',
                'pic_courses.role as pic_course_role',
                'pic_courses.avatar as pic_course_avatar',
                'pic_courses.created_at as pic_course_created_at',
                'pic_courses.updated_at as pic_course_updated_at',

                'teachers.id as teachers_id',
                'teachers.name as teachers_name',
                'teachers.username as teachers_username',
                'teachers.role as teachers_role',
                'teachers.avatar as teachers_avatar',
                'teachers.created_at as teachers_created_at',
                'teachers.updated_at as teachers_updated_at',
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
}
