<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = DB::select(
            'SELECT users.id
            FROM users
            INNER JOIN roles
            ON users.role_id = roles.id
            WHERE roles.name = ?',
            ['STUDENT']
        );

        foreach ($students as $key => $student) {
            DB::insert(
                'INSERT INTO students (user_id, created_at)
                VALUES (:user_id, :created_at)',
                [
                    'user_id' => $student->id,
                    'created_at' => Carbon::now()
                ]
            );
        }
    }
}
