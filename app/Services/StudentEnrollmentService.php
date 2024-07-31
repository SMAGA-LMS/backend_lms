<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponseDto;
use App\DataTransferObjects\StudentEnrollments\StudentEnrollmentResponseDto;
use App\Models\Role;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\DB;

class StudentEnrollmentService
{
    public function getAllEnrolledStudentClassPeriod($classPeriodCode): ServiceResponseDto
    {

        $classPeriodId = DB::selectOne(
            'SELECT id
            FROM class_periods
            WHERE class_period_code = :class_period_code',
            [
                'class_period_code' => $classPeriodCode
            ]
        );

        if (!$classPeriodId) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Class period not found.",
                errors: [],
                data: null,
                codeResponse: 404
            );
        }

        $enrolledStudents = DB::select(
            'SELECT
                se.id AS student_enrollment_id,
                s.user_id AS user_id,
                u.user_code AS user_code,
                u.full_name AS user_full_name,
                u.avatar AS user_avatar
            FROM student_enrollments AS se
            JOIN students AS s ON se.student_id = s.user_id
            JOIN users AS u ON s.user_id = u.id
            WHERE class_period_id = :class_period_id',
            [
                'class_period_id' => $classPeriodId->id
            ]
        );

        // Convert the result to an array
        $dataArray = array_map(function ($item) {
            return new StudentEnrollmentResponseDto(
                $item->student_enrollment_id,
                $item->user_id,
                $item->user_code,
                $item->user_full_name,
                $item->user_avatar ?? null
            );
        }, $enrolledStudents);

        return new ServiceResponseDto(
            isSuccess: true,
            message: "Success get all enrolled student in " . $classPeriodCode,
            errors: [],
            data: $dataArray,
            codeResponse: 200
        );
    }
}
