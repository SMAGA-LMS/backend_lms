<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CourseModule extends Model
{
    use HasFactory;

    public $table = 'course_modules';

    protected $fillable = [
        'course_id',
        'module_id',
    ];


    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getCourseModulesByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->join('modules', 'course_modules.module_id', '=', 'modules.id')
            ->join('courses', 'course_modules.course_id', '=', 'courses.id')
            ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            ->select(
                'course_modules.*',

                'modules.name as module_name',
                'modules.description as module_description',
                'modules.file as module_file',
                'modules.created_at as module_created_at',
                'modules.updated_at as module_updated_at',

                'courses.name as course_name',
                'courses.user_id as course_user_id',
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


    public function insertNewCourseModule(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function getCourseModuleByID($id)
    {
        return DB::table($this->table)
            ->join('modules', 'course_modules.module_id', '=', 'modules.id')
            ->join('courses', 'course_modules.course_id', '=', 'courses.id')
            ->leftJoin('users as pic_courses', 'courses.user_id', '=', 'pic_courses.id')
            ->select(
                'course_modules.*',

                'modules.name as module_name',
                'modules.description as module_description',
                'modules.file as module_file',
                'modules.created_at as module_created_at',
                'modules.updated_at as module_updated_at',

                'courses.name as course_name',
                'courses.user_id as course_user_id',
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
            )
            ->where('course_modules.id', $id)
            ->first();
    }
}
