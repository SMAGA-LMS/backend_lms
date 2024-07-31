<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = [
            [
                'name' => 'A1',
                'created_at' => now()
            ],
            [
                'name' => 'A2',
                'created_at' => now()
            ],
        ];

        foreach ($classrooms as $classroom) {
            DB::insert(
                'INSERT INTO classrooms (name, created_at)
                VALUES (:name, :created_at)',
                [
                    'name' => $classroom['name'],
                    'created_at' => $classroom['created_at'],
                ]
            );
        }
    }
}
