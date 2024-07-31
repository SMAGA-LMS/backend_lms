<?php

namespace App\Services;

use App\DataTransferObjects\ClassPeriods\AddNewClassPeriodRequestDto;
use App\DataTransferObjects\ClassPeriods\ClassPeriodResponseDto;
use App\DataTransferObjects\ClassPeriods\DetailClassPeriodResponseDto;
use App\DataTransferObjects\ServiceResponseDto;
use App\Models\AcademicTerm;
use App\Models\ClassPeriod;
use App\Utils\ClassPeriodUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ClassPeriodService
{

    protected $classPeriodUtil;
    /**
     * Create a new class instance.
     */
    public function __construct(ClassPeriodUtil $classPeriodUtil)
    {
        $this->classPeriodUtil = $classPeriodUtil;
    }

    public function getSpecificClassPeriodList($academic_term_id): ServiceResponseDto
    {
        $academicTerm = DB::selectOne(
            'SELECT *
            FROM academic_terms
            WHERE id = :academic_term_id',
            [
                'academic_term_id' => $academic_term_id
            ]
        );

        if (empty($academicTerm)) {
            return new ServiceResponseDto(
                isSuccess: true,
                message: "No class period found.",
                errors: [],
                data: [],
                codeResponse: 200
            );
        }

        // kalau mau akses dari foreign key terus akses ke table lain lagi.
        // Misal dari table class_period mau dapetin grade_level di mana harus lewat dulu ke grade_classroom, pakenya classPeriod::with([gradeClassroom.gradeLevel])

        $data = DB::select(
            'SELECT
                cp.id  AS class_period_id,
                cp.class_period_code AS class_period_code,
                gl.name AS grade_level_name,
                c.name AS classroom_name,
                at.name AS academic_term_name,
                cp.name AS class_period_name,
                COUNT(se.id) AS total_students_enrolled
            FROM class_periods AS cp
            JOIN grade_classrooms AS gc ON cp.grade_classroom_id = gc.id
            JOIN grade_levels AS gl ON gc.grade_level_id = gl.id
            JOIN classrooms AS c ON gc.classroom_id = c.id
            JOIN academic_terms AS at ON cp.academic_term_id = at.id
            LEFT JOIN student_enrollments AS se ON cp.id = se.class_period_id
            WHERE academic_term_id = :academic_term_id
            GROUP BY cp.id, cp.class_period_code, gl.name, c.name, at.name -- perlu group by karena ada operasi COUNT
            ORDER BY at.name DESC',
            [
                'academic_term_id' => $academic_term_id
            ]
        );

        // Convert the result to an array object
        $dataArray = array_map(function ($item) {
            return new ClassPeriodResponseDto(
                $item->class_period_id,
                $item->class_period_code,
                $item->grade_level_name,
                $item->classroom_name,
                $item->academic_term_name,
                $item->class_period_name,
                $item->total_students_enrolled
            );
        }, $data);

        $message = "List of class period " . $academicTerm->name . " retrieved successfully.";
        if (empty($dataArray)) $message = "No class period " . $academicTerm->name . " found.";

        return new ServiceResponseDto(
            isSuccess: true,
            message: $message,
            errors: [],
            data: $dataArray,
            codeResponse: 200
        );
    }

    public function getAllClassPeriodList(): ServiceResponseDto
    {
        $data = DB::select(
            'SELECT
                cp.id  AS class_period_id,
                cp.class_period_code AS class_period_code,
                gl.name AS grade_level_name,
                c.name AS classroom_name,
                at.name AS academic_term_name,
                cp.name AS class_period_name,
                COUNT(se.id) AS total_students_enrolled
            FROM class_periods AS cp
            JOIN grade_classrooms AS gc ON cp.grade_classroom_id = gc.id
            JOIN grade_levels AS gl ON gc.grade_level_id = gl.id
            JOIN classrooms AS c ON gc.classroom_id = c.id
            JOIN academic_terms AS at ON cp.academic_term_id = at.id
            LEFT JOIN student_enrollments AS se ON cp.id = se.class_period_id
            GROUP BY cp.id, cp.class_period_code, gl.name, c.name, at.name  -- perlu group by karena ada operasi COUNT
            ORDER BY at.name DESC',
        );

        // Convert the result to an array
        $dataArray = array_map(function ($item) {
            return new ClassPeriodResponseDto(
                $item->class_period_id,
                $item->class_period_code,
                $item->grade_level_name,
                $item->classroom_name,
                $item->academic_term_name,
                $item->class_period_name,
                $item->total_students_enrolled
            );
        }, $data);

        $message = "List of all class period retrieved successfully.";
        if (empty($dataArray)) $message = "No class period found.";

        return new ServiceResponseDto(
            isSuccess: true,
            message: $message,
            errors: [],
            data: $dataArray,
            codeResponse: 200
        );
    }

    public function getDetailClassPeriod($classPeriodCode): ServiceResponseDto
    {
        $classPeriodData = DB::selectOne(
            'SELECT
                cp.id AS class_period_id, cp.name AS class_period_name, cp.class_period_code AS class_period_code,
                u.full_name AS teacher_full_name,
                u.user_code AS teacher_code,
                u.avatar AS avatar_teacher
            FROM class_periods AS cp
            LEFT JOIN teachers AS t ON cp.teacher_id = t.user_id
            LEFT JOIN users AS u ON t.user_id = u.id
            WHERE class_period_code = :class_period_code
            LIMIT 1',
            [
                'class_period_code' => $classPeriodCode
            ]
        );

        if (!$classPeriodData) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Class period not found.",
                errors: [],
                data: null,
                codeResponse: 404
            );
        }

        // Convert the result to an array
        $data = new DetailClassPeriodResponseDto(
            $classPeriodData->class_period_id,
            $classPeriodData->class_period_name,
            $classPeriodData->class_period_code,
            $classPeriodData->teacher_full_name ?? null,
            $classPeriodData->teacher_code ?? null,
            $classPeriodData->avatar_teacher ?? null
        );


        $message = "Detail class period retrieved successfully.";

        return new ServiceResponseDto(
            isSuccess: true,
            message: $message,
            errors: [],
            data: $data,
            codeResponse: 200
        );
    }

    public function addNewClassPeriod(AddNewClassPeriodRequestDto $addNewClassPeriodRequestDto): ServiceResponseDto
    {
        $classPeriod = DB::selectOne(
            'SELECT id, class_period_code
            FROM class_periods
            WHERE grade_classroom_id = :grade_classroom_id
                AND academic_term_id = :academic_term_id',
            [
                'grade_classroom_id' => $addNewClassPeriodRequestDto->grade_classroom_id,
                'academic_term_id' => $addNewClassPeriodRequestDto->academic_term_id,
            ]
        );

        $newClassPeriod = null;
        if ($classPeriod) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Class period already exist with this class period code: " . $classPeriod->class_period_code,
                errors: [],
                data: null,
                codeResponse: 409
            );
        }

        try {
            $classPeriodCode = $this->classPeriodUtil->generateClassPeriodCode(
                gradeClassroomId: $addNewClassPeriodRequestDto->grade_classroom_id,
                academicTermId: $addNewClassPeriodRequestDto->academic_term_id
            );

            $classPeriodName = $this->classPeriodUtil->generateClassPeriodName(
                gradeClassroomId: $addNewClassPeriodRequestDto->grade_classroom_id,
                academicTermId: $addNewClassPeriodRequestDto->academic_term_id
            );

            DB::insert(
                'INSERT INTO class_periods (grade_classroom_id, academic_term_id, teacher_id, class_period_code, name, created_at)
                VALUES (:grade_classroom_id, :academic_term_id, :teacher_id, :class_period_code, :name, :created_at)',
                [
                    'grade_classroom_id' => $addNewClassPeriodRequestDto->grade_classroom_id,
                    'academic_term_id' => $addNewClassPeriodRequestDto->academic_term_id,
                    'teacher_id' => $addNewClassPeriodRequestDto->user_id,
                    'class_period_code' => $classPeriodCode,
                    'name' => $classPeriodName,
                    'created_at' => Carbon::now()
                ]
            );

            $newClassPeriod = ClassPeriod::where('class_period_code', $classPeriodCode)->first();
        } catch (Exception $e) {
            return new ServiceResponseDto(
                isSuccess: false,
                message: "Error, can't add new class period. Grade Classroom, or Academic Term, or User ID is not valid",
                errors: [$e->getMessage()],
                data: null,
                codeResponse: 500
            );
        }

        $classPeriodData = DB::selectOne(
            'SELECT
                cp.id AS class_period_id, cp.name AS class_period_name, cp.class_period_code AS class_period_code,
                u.full_name AS teacher_full_name,
                u.user_code AS teacher_code,
                u.avatar AS avatar_teacher
            FROM class_periods AS cp
            LEFT JOIN teachers AS t ON cp.teacher_id = t.user_id
            LEFT JOIN users AS u ON t.user_id = u.id
            WHERE class_period_code = :class_period_code
            LIMIT 1',
            [
                'class_period_code' => $newClassPeriod->class_period_code
            ]
        );

        // Convert the result to an array
        $data = new DetailClassPeriodResponseDto(
            $classPeriodData->class_period_id,
            $classPeriodData->class_period_name,
            $classPeriodData->class_period_code,
            $classPeriodData->teacher_full_name ?? null,
            $classPeriodData->teacher_code ?? null,
            $classPeriodData->avatar_teacher ?? null
        );

        return new ServiceResponseDto(
            isSuccess: true,
            message: "Success add new class period.",
            errors: [],
            data: $data,
            codeResponse: 201
        );
    }
}
