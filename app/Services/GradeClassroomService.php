<?php

namespace App\Services;

use App\DataTransferObjects\GradeClassrooms\GradeClassroomResponseDto;
use App\DataTransferObjects\ServiceResponseDto;
use Illuminate\Support\Facades\DB;

class GradeClassroomService
{
    public function getAllGradeClassrooms(): ServiceResponseDto
    {
        $dataGradeClassroom = DB::select(
            'SELECT
                gc.id AS grade_classroom_id,
                CONCAT(
                    gl.name,
                    " ",
                    c.name
                ) AS grade_classroom_name
            FROM grade_classrooms AS gc
            JOIN grade_levels AS gl ON gc.grade_level_id = gl.id
            JOIN classrooms AS c ON gc.classroom_id = c.id
            ORDER BY grade_classroom_name ASC'
        );


        // Convert the result to an array
        $dataArray = array_map(function ($item) {
            return new GradeClassroomResponseDto(
                $item->grade_classroom_id,
                $item->grade_classroom_name
            );
        }, $dataGradeClassroom);

        $message = "Grade classroom retrieved successfully.";

        return new ServiceResponseDto(
            isSuccess: true,
            message: $message,
            errors: [],
            data: $dataArray,
            codeResponse: 200
        );
    }
}
