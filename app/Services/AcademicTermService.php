<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponseDto;
use App\Helpers\ApiResponseHelper;
use App\Models\AcademicTerm;

class AcademicTermService
{


    public function getAllAcademicTerms(): ServiceResponseDto
    {
        $academicTerms = AcademicTerm::all()->sortByDesc('name');

        return new ServiceResponseDto(
            isSuccess: true,
            message: "Success get all available academic terms.",
            errors: [],
            data: $academicTerms,
            codeResponse: 200
        );
    }
}
