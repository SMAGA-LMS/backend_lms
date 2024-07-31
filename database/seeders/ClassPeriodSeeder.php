<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classPeriods = [];

        for ($i = 1; $i <= 2; $i++) {
            for ($j = 1; $j <= 6; $j++) {
                $classPeriod = [
                    'grade_classroom_id' => $j,
                    'academic_term_id' => $i
                ];
                $classPeriods[] = $classPeriod;
            }
        }

        $classPeriods[] = [
            'grade_classroom_id' => 1,
            'academic_term_id' => 3
        ];


        foreach ($classPeriods as $classPeriod) {
            $gradeClassroomName = DB::selectOne(
                'SELECT CONCAT(gl.name, " ", c.name) AS grade_classroom_name
                FROM grade_classrooms AS gc
                JOIN grade_levels AS gl ON gc.grade_level_id = gl.id
                JOIN classrooms AS c ON gc.classroom_id = c.id
                WHERE gc.id = :grade_classroom_id',
                ['grade_classroom_id' => $classPeriod['grade_classroom_id']]
            )->grade_classroom_name;

            $academicTerm = DB::selectOne(
                'SELECT
                    name,
                    CONCAT(
                        SUBSTRING(name, 3, 2), -- 2023/2024 -> 23
                        SUBSTRING(name, 8, 9) -- 2023/2024 -> 24
                    ) AS period_name
                FROM academic_terms
                WHERE id = :academic_term_id',
                ['academic_term_id' => $classPeriod['academic_term_id']]
            );

            $classPeriodCode = str_replace(' ', '', $gradeClassroomName . $academicTerm->period_name);
            $classPeriodName = $gradeClassroomName . " - " . $academicTerm->name;

            DB::insert(
                'INSERT INTO class_periods (grade_classroom_id, academic_term_id, class_period_code, name, created_at)
                VALUES (:grade_classroom_id, :academic_term_id, :class_period_code, :name, :created_at)',
                [
                    'grade_classroom_id' => $classPeriod['grade_classroom_id'],
                    'academic_term_id' => $classPeriod['academic_term_id'],
                    'class_period_code' => $classPeriodCode,
                    'name' => $classPeriodName,
                    'created_at' => Carbon::now()
                ]
            );
        }

        //

        // foreach ($classPeriods as $classPeriod) {
        //     DB::insert(
        //         'INSERT INTO class_periods (grade_classroom_id, academic_term_id, created_at)
        //         VALUES (:grade_classroom_id, :academic_term_id, :created_at)',
        //         [
        //             'grade_classroom_id' => $classPeriod['grade_classroom_id'],
        //             'academic_term_id' => $classPeriod['academic_term_id'],
        //             'created_at' => Carbon::now()
        //         ]
        //     );
        // }

        // $data = DB::select(
        //     "SELECT
        //         cp.id AS class_period_id,
        //         CONCAT(
        //             CONCAT(gl.name, c.name), -- X + A1 -> XA1
        //             SUBSTRING(at.name, 3, 2), -- 2023/2024 -> 23
        //             SUBSTRING(at.name, 8, 9) -- 2023/2024 -> 24
        //         ) AS class_period_code
        //     FROM class_periods AS cp
        //     JOIN grade_classrooms AS gc ON cp.grade_classroom_id = gc.id
        //     JOIN grade_levels AS gl ON gc.grade_level_id = gl.id
        //     JOIN classrooms AS c ON gc.classroom_id = c.id
        //     JOIN academic_terms AS at ON cp.academic_term_id = at.id"
        // );

        // foreach ($data as $item) {
        //     DB::update(
        //         'UPDATE class_periods
        //         SET class_period_code = :data_class_period_code,
        //         updated_at = :now
        //         WHERE id = :data_cp_id',
        //         [
        //             'data_cp_id' => $item->class_period_id,
        //             'data_class_period_code' => $item->class_period_code,
        //             'now' => Carbon::now()
        //         ]
        //     );
        // }
    }
}
