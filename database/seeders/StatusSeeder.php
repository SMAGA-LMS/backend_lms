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
                'name' => 'ACTIVE',
                'created_at' => now(),
            ],
            [
                'name' => 'INACTIVE',
                'created_at' => now(),
            ],
            [
                'name' => 'GRADUATED',
                'created_at' => now(),
            ],
            [
                'name' => 'DROPPED',
                'created_at' => now(),
            ],
        ];

        foreach ($statuses as $status) {
            DB::insert(
                'INSERT INTO statuses (name, created_at)
                VALUES (:name, :created_at)',
                [
                    $status['name'],
                    $status['created_at'],
                ]
            );
        }
    }
}
