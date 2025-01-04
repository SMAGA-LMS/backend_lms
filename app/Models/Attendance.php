<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attendance extends Model
{
    use HasFactory;

    public $table = 'attendances';

    protected $fillable = [
        'student_id',
        'session_record_id',
        'status',

        // ga jadi dipakai, dialihkan ke table session_records
        // 'classenrollment_id',
        // 'date_time',
        // 'session',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getAttendancesByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->join('session_records', 'session_records.id', '=', 'attendances.session_record_id')
            ->join('users', 'users.id', '=', 'attendances.student_id')
            ->select(
                'attendances.*',

                'session_records.class_enrollment_id as session_record_class_enrollment_id',
                'session_records.title as session_record_title',
                'session_records.description as session_record_description',
                'session_records.date_time as session_record_date_time',
                'session_records.created_at as session_record_created_at',
                'session_records.updated_at as session_record_updated_at',

                'users.name as student_name',
                'users.username as student_username',
                'users.role as student_role',
                'users.avatar as student_avatar',
                'users.created_at as student_created_at',
                'users.updated_at as student_updated_at',

                'attendances.created_at as attendance_created_at',
                'attendances.updated_at as attendance_updated_at',
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

    public function insertNewAttendance(array $data)
    {
        $data['created_at'] = now();
        return DB::table($this->table)->insertGetId($data);
    }

    public function getAttendancesForStudent($studentID, $classEnrollmentID)
    {
        return DB::table($this->table)
            ->join('session_records', 'session_records.id', '=', 'attendances.session_record_id')
            ->join('users', 'users.id', '=', 'attendances.student_id')
            ->select(
                'attendances.*',

                'session_records.class_enrollment_id as session_record_class_enrollment_id',
                'session_records.title as session_record_title',
                'session_records.description as session_record_description',
                'session_records.date_time as session_record_date_time',
                'session_records.created_at as session_record_created_at',
                'session_records.updated_at as session_record_updated_at',

                'users.name as student_name',
                'users.username as student_username',
                'users.role as student_role',
                'users.avatar as student_avatar',
                'users.created_at as student_created_at',
                'users.updated_at as student_updated_at',

                'attendances.created_at as attendance_created_at',
                'attendances.updated_at as attendance_updated_at',
            )
            ->where($this->table . '.student_id', $studentID)
            ->where('session_records.class_enrollment_id', $classEnrollmentID) // Add table prefix
            ->get();
    }
}
