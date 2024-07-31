<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gradeLevels = DB::select(
            'SELECT id
            FROM grade_levels'
        );

        $classroomNames = DB::select(
            'SELECT id
            FROM classrooms'
        );

        $gradeClassrooms = [];

        foreach ($gradeLevels as $gradeLevel) {
            foreach ($classroomNames as $classroom) {
                $gradeClassrooms[] = [
                    'grade_level_id' => $gradeLevel->id,
                    'classroom_id' => $classroom->id
                ];
            }
        }

        foreach ($gradeClassrooms as $gradeClassroom) {
            DB::insert(
                'INSERT INTO grade_classrooms (grade_level_id, classroom_id, created_at)
                VALUES (:grade_level_id, :classroom_id, :created_at)',
                [
                    'grade_level_id' => $gradeClassroom['grade_level_id'],
                    'classroom_id' => $gradeClassroom['classroom_id'],
                    'created_at' => Carbon::now(),
                ]
            );
        }
    }
}
