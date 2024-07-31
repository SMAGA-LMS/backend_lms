<?php

namespace App\Utils;

use Illuminate\Support\Facades\DB;

class ClassPeriodUtil
{
    private function getGradeClassroomName(int $gradeClassroomId): string
    {
        return DB::selectOne(
            'SELECT CONCAT(gl.name, " ", c.name) AS grade_classroom_name
            FROM grade_classrooms AS gc
            JOIN grade_levels AS gl ON gc.grade_level_id = gl.id
            JOIN classrooms AS c ON gc.classroom_id = c.id
            WHERE gc.id = :grade_classroom_id',
            ['grade_classroom_id' => $gradeClassroomId]
        )->grade_classroom_name;
    }

    private function getAcademicTerm(int $academicTermId): object
    {
        return DB::selectOne(
            'SELECT
                name,
                CONCAT(
                    SUBSTRING(name, 3, 2), -- 2023/2024 -> 23
                    SUBSTRING(name, 8, 9) -- 2023/2024 -> 24
                ) AS period_name
            FROM academic_terms
            WHERE id = :academic_term_id',
            ['academic_term_id' => $academicTermId]
        );
    }

    public function generateClassPeriodCode(int $gradeClassroomId, int $academicTermId): string
    {
        $gradeClassroomName = $this->getGradeClassroomName($gradeClassroomId);
        $academicTerm = $this->getAcademicTerm($academicTermId);

        $classPeriodCode = $gradeClassroomName . $academicTerm->period_name;

        // Menghapus semua spasi dari hasil gabungan
        return str_replace(' ', '', $classPeriodCode);
    }

    public function generateClassPeriodName(int $gradeClassroomId, int $academicTermId): string
    {
        $gradeClassroomName = $this->getGradeClassroomName($gradeClassroomId);
        $academicTerm = $this->getAcademicTerm($academicTermId);

        return $gradeClassroomName . " - " . $academicTerm->name;
    }
}
