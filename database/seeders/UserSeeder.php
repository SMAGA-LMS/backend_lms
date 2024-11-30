<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin SMAGA',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'ADMIN',
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teacher SMAGA',
                'username' => 'teacher',
                'password' => Hash::make('teacher'),
                'role' => 'TEACHER',
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Student SMAGA',
                'username' => 'student',
                'password' => Hash::make('student'),
                'role' => 'STUDENT',
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
