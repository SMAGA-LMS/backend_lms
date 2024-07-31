<?php

namespace App\DataTransferObjects\ClassPeriods;

class DetailClassPeriodResponseDto
{
    public function __construct(
        public readonly string $class_period_id,
        public readonly string $class_period_name,
        public readonly string $class_period_code,
        public readonly mixed $teacher_full_name,
        public readonly mixed $teacher_code,
        public readonly mixed $avatar_teacher
    ) {
    }
}
