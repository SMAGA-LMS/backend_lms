<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'status_type' => 'Active',
                'created_at' => now(),
            ],
            [
                'status_type' => 'Inactive',
                'created_at' => now(),
            ],
            [
                'status_type' => 'Graduated',
                'created_at' => now(),
            ],
            [
                'status_type' => 'Dropped',
                'created_at' => now(),
            ],
        ];

        foreach ($statuses as $status) {
            DB::insert(
                'INSERT INTO statuses (status_type, created_at)
                VALUES (:status_type, :created_at)',
                [
                    $status['status_type'],
                    $status['created_at'],
                ]
            );
        }
    }
}
