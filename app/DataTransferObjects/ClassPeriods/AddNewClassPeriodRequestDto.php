<?php

namespace App\DataTransferObjects\ClassPeriods;

class AddNewClassPeriodRequestDto
{
    public function __construct(
        public readonly int $grade_classroom_id,
        public readonly int $academic_term_id,
        public readonly mixed $user_id
    ) {
    }
}
