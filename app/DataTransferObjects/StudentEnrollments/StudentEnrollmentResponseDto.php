<?php

namespace App\DataTransferObjects\StudentEnrollments;

class StudentEnrollmentResponseDto
{
    public function __construct(
        public readonly int $student_enrollment_id,
        public readonly int $user_id,
        public readonly string $user_code,
        public readonly string $user_full_name,
        public readonly mixed $user_avatar
    ) {
    }
}
