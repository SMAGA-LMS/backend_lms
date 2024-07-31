<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataClassPeriods = DB::select(
            'SELECT
                id AS class_period_id,
                academic_term_id AS class_period_academic_term_id,
                COUNT(id) AS total_class_period
            FROM class_periods
            WHERE academic_term_id = 1
            GROUP BY id, academic_term_id
            '
        );

        $dataStudents = DB::select(
            'SELECT
                user_id AS student_id,
                COUNT(user_id) AS total_student
            FROM students
            GROUP BY user_id
            '
        );

        $studentEnrollments = [];
        $count = 1;
        foreach ($dataStudents as $student) {
            if ($count >= 7) $count = 1;

            if ($count <= 6) {
                $studentEnrollments[] = [
                    'class_period_id' => $dataClassPeriods[$count - 1]->class_period_id,
                    'student_id' => $student->student_id
                ];
            }
            $count++;
        }

        foreach ($studentEnrollments as $studentEnrollment) {
            DB::insert(
                'INSERT INTO student_enrollments (class_period_id, student_id, created_at)
                VALUES (:class_period_id, :student_id, :created_at)',
                [
                    'class_period_id' => $studentEnrollment['class_period_id'],
                    'student_id' => $studentEnrollment['student_id'],
                    'created_at' => Carbon::now()
                ]
            );
        }
    }
}
