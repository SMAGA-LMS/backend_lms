<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = DB::select(
            'SELECT users.id, users.gender
            FROM users
            INNER JOIN roles
            ON users.role_id = roles.id
            WHERE roles.name = ?',
            ['TEACHER']
        );

        // $tmCounter = 1;
        // $tfCounter = 1;

        foreach ($teachers as $teacher) {
            // if ($teacher->gender === 'MALE') {
            //     $prefix = 'TM';
            //     $teacherCode = $prefix . $tmCounter++;
            // } else {
            //     $prefix = 'TF';
            //     $teacherCode = $prefix . $tfCounter++;
            // }

            DB::insert(
                'INSERT INTO teachers (user_id, created_at)
                VALUES (:user_id, :created_at)',
                [
                    'user_id' => $teacher->id,
                    'created_at' => Carbon::now()
                ]
            );
        }
    }
}
