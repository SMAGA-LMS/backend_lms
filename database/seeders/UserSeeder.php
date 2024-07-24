<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $users = [
            [
                'role_id' => 1,
                'status_id' => 1,
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'full_name' => 'admin',
                'email' => 'contact@binus.ac.id',
                'created_at' => now()
            ],
            [
                'role_id' => 1,
                'status_id' => 1,
                'username' => 'adminsmaga',
                'password' => Hash::make('adminsmaga'),
                'full_name' => 'admin smaga 1',
                'gender' => 'male',
                'created_at' => now()
            ],
            [
                'role_id' => 2,
                'status_id' => 1,
                'username' => 'studentsmaga',
                'password' => Hash::make('studentsmaga'),
                'full_name' => 'student smaga',
                'gender' => 'male',
                'created_at' => now()
            ],
            [
                'role_id' => 3,
                'status_id' => 1,
                'username' => 'teachersmaga',
                'password' => Hash::make('teachersmaga'),
                'full_name' => 'teacher smaga',
                'gender' => 'female',
                'created_at' => now()
            ]
        ];

        DB::table('users')->insert($users);
    }
}
