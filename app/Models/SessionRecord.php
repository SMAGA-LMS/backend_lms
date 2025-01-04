<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SessionRecord extends Model
{
    use HasFactory;

    public $table = 'session_records';

    protected $fillable = [
        'class_enrollment_id',
        'title',
        'description',
        'date_time',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getSessionRecordsByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->join('class_enrollments', 'session_records.class_enrollment_id', '=', 'class_enrollments.id')
            ->select(
                'session_records.*',

                'class_enrollments.classroom_id as classroom_id',
                'class_enrollments.course_id as course_id',
                'class_enrollments.user_id as teacher_id',
                'class_enrollments.created_at as class_enrollment_created_at',
                'class_enrollments.updated_at as class_enrollment_updated_at',
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

    public function insertNewSessionRecord(array $data)
    {
        $data['created_at'] = now();
        return DB::table($this->table)->insertGetId($data);
    }

    public function getSessionRecordDetailByID($id)
    {
        return DB::table($this->table)
            ->join('class_enrollments', 'session_records.class_enrollment_id', '=', 'class_enrollments.id')
            ->join('classrooms', 'class_enrollments.classroom_id', '=', 'classrooms.id')
            ->join('courses', 'class_enrollments.course_id', '=', 'courses.id')
            ->select(
                'session_records.*',

                'class_enrollments.classroom_id as classroom_id',
                'class_enrollments.course_id as course_id',
                'class_enrollments.user_id as teacher_id',
                'class_enrollments.created_at as class_enrollment_created_at',
                'class_enrollments.updated_at as class_enrollment_updated_at',

                'classrooms.name as classroom_name',
                'classrooms.grade as classroom_grade',
                'classrooms.created_at as classroom_created_at',
                'classrooms.updated_at as classroom_updated_at',

                'courses.name as course_name',
                'courses.grade as course_grade',
                'courses.created_at as course_created_at',
                'courses.updated_at as course_updated_at',
            )
            ->where('session_records.id', $id)
            ->first();
    }
}
