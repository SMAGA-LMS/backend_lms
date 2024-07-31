<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'ADMIN',
                'created_at' => now()
            ],
            [
                'name' => 'TEACHER',
                'created_at' => now()
            ],
            [
                'name' => 'STUDENT',
                'created_at' => now()
            ]
        ];

        foreach ($roles as $role) {
            DB::insert(
                'INSERT INTO roles (name, created_at)
                VALUES (:name, :created_at)',
                [
                    $role['name'],
                    $role['created_at'],
                ]
            );
        }
    }
}
