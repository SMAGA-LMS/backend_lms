<?php

namespace Database\Seeders;

use App\Models\ClassPeriod;
use App\Models\GradeClassroom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            StatusSeeder::class,
            UserSeeder::class,
            GradeLevelSeeder::class,
            ClassroomSeeder::class,
            GradeClassroomSeeder::class,
            TeacherSeeder::class,
            AcademicTermSeeder::class,
            ClassPeriodSeeder::class,
            StudentSeeder::class,
            StudentEnrollmentSeeder::class,
            AdminSeeder::class
        ]);
    }
}
