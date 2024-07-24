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
                'role_name' => 'Admin',
                'created_at' => now()
            ],
            [
                'role_name' => 'Student',
                'created_at' => now()
            ],
            [
                'role_name' => 'Teacher',
                'created_at' => now()
            ]
        ];

        foreach ($roles as $role) {
            DB::insert(
                'INSERT INTO roles (role_name, created_at)
                VALUES (:role_name, :created_at)',
                [
                    $role['role_name'],
                    $role['created_at'],
                ]
            );
        }
    }
}
