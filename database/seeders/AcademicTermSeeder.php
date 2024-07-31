<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        $academic_terms = [];

        for ($i = -1; $i <= 1; $i++) {
            $year = $currentYear + $i;
            $startDate = (new \DateTime("$year-07-01"))->format('Y-m-d');
            $endDate = (new \DateTime(($year + 1) . "-06-01"))->format('Y-m-d');

            $academic_terms[] = [
                'name' => $year . '/' . ($year + 1),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => now()
            ];
        }

        foreach ($academic_terms as $academic_term) {
            DB::insert(
                'INSERT INTO academic_terms (name, start_date, end_date, created_at)
                VALUES (:name, :start_date, :end_date, :created_at)',
                [
                    'name' => $academic_term['name'],
                    'start_date' => $academic_term['start_date'],
                    'end_date' => $academic_term['end_date'],
                    'created_at' => $academic_term['created_at'],
                ]
            );
        }
    }
}
