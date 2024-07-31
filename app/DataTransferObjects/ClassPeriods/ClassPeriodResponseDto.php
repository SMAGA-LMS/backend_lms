<?php

namespace App\DataTransferObjects\ClassPeriods;

class ClassPeriodResponseDto
{
    public readonly int $class_period_id;
    public readonly string $class_period_code;
    public readonly string $grade_level_name;
    public readonly string $classroom_name;
    public readonly string $academic_term_name;
    public readonly string $class_period_name;
    public readonly string $total_students_enrolled;

    public function __construct(
        int $class_period_id,
        string $class_period_code,
        string $grade_level_name,
        string $classroom_name,
        string $academic_term_name,
        string $class_period_name,
        string $total_students_enrolled
    ) {
        $this->class_period_id = $class_period_id;
        $this->class_period_code = $class_period_code;
        $this->grade_level_name = $grade_level_name;
        $this->classroom_name = $classroom_name;
        $this->academic_term_name = $academic_term_name;
        $this->class_period_name = $class_period_name;
        $this->total_students_enrolled = $total_students_enrolled;
    }
}
