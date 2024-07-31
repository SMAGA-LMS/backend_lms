<?php

namespace App\DataTransferObjects\GradeClassrooms;

class GradeClassroomResponseDto
{
    public function __construct(
        public readonly int $grade_classroom_id,
        public readonly string $grade_classroom_name
    ) {
    }
}
