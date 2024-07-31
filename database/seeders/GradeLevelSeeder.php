<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grade_levels = [
            [
                'name' => 'X',
                'created_at' => now()
            ],
            [
                'name' => 'XI',
                'created_at' => now()
            ],
            [
                'name' => 'XII',
                'created_at' => now()
            ],
        ];

        foreach ($grade_levels as $grade_level) {
            DB::insert(
                'INSERT INTO grade_levels (name, created_at)
                VALUES (:name, :created_at)',
                [
                    'name' => $grade_level['name'],
                    'created_at' => $grade_level['created_at'],
                ]
            );
        }
    }
}
