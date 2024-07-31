<?php

namespace Database\Seeders;

use App\Enums\UserGenderEnum;
use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Utils\UserUtil;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{


    protected $userUtil;
    /**
     * Create a new class instance.
     */
    public function __construct(UserUtil $userUtil)
    {
        $this->userUtil = $userUtil;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'role_id' => UserRoleEnum::ADMIN, // admin
                'status_id' => 1,
                'user_code' => 'AF1',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'full_name' => 'Admin SMAGA',
                'gender' => UserGenderEnum::FEMALE,
                'created_at' => now()
            ],
            [
                'role_id' => UserRoleEnum::TEACHER, // teacher
                'status_id' => 1,
                'user_code' => 'TM1',
                'username' => 'teachersmaga',
                'password' => Hash::make('teachersmaga'),
                'full_name' => 'Teacher SMAGA',
                'gender' => UserGenderEnum::MALE,
                'created_at' => now()
            ],
            [
                'role_id' => UserRoleEnum::STUDENT, // student
                'status_id' => 1,
                'user_code' => '8870',
                'username' => 'syauqi',
                'password' => Hash::make('syauqi'),
                'full_name' => 'M. Syauqi F',
                'gender' => UserGenderEnum::MALE,
                'created_at' => now()
            ],
            [
                'role_id' => UserRoleEnum::STUDENT, // student
                'status_id' => 1,
                'user_code' => '8871',
                'username' => 'kevin',
                'password' => Hash::make('kevin'),
                'full_name' => 'Kevin A.',
                'gender' => UserGenderEnum::MALE,
                'created_at' => now()
            ],
            [
                'role_id' => UserRoleEnum::TEACHER, // teacher
                'status_id' => 1,
                'user_code' => 'TM2',
                'username' => 'ortuhanel',
                'password' => Hash::make('ortuhanel'),
                'full_name' => 'Ortu Han Elta.',
                'gender' => UserGenderEnum::MALE,
                'created_at' => now()
            ],
            [
                'role_id' => UserRoleEnum::TEACHER, // teacher
                'status_id' => 1,
                'user_code' => 'TF1',
                'username' => 'evatanuar',
                'password' => Hash::make('evatanuar'),
                'full_name' => 'Eva Tanuar.',
                'gender' => UserGenderEnum::FEMALE,
                'created_at' => now()
            ],
            [
                'role_id' => UserRoleEnum::TEACHER, // teacher
                'status_id' => 1,
                'user_code' => 'TM3',
                'username' => 'ekakurniawan',
                'password' => Hash::make('ekakurniawan'),
                'full_name' => 'Eka Kurniawan.',
                'gender' => UserGenderEnum::MALE,
                'created_at' => now()
            ]
        ];

        foreach ($users as $key => $user) {
            $user['user_code'] = $this->userUtil->generateUserCode($user['role_id'], $user['gender']);
        }

        $faker = Faker::create();

        for ($i = 0; $i < 16; $i++) {
            $gender = $faker->randomElement([UserGenderEnum::MALE, UserGenderEnum::FEMALE]);
            $fullName = $faker->name($gender);
            $username = $this->userUtil->generateUsername($fullName);

            $users[] = [
                'role_id' => UserRoleEnum::STUDENT,
                'status_id' => 1,
                'user_code' => "89" . $i + 10,
                'username' => $username,
                'password' => Hash::make($username),
                'full_name' => $fullName,
                'gender' => strtoupper($gender),
                'created_at' => now()
            ];
        }


        foreach ($users as $user) {
            DB::insert(
                'INSERT INTO users (role_id, status_id, user_code, username, password, full_name, gender)
                VALUES (:role_id, :status_id, :user_code, :username, :password, :full_name, :gender)',
                [
                    'role_id' => $user['role_id'],
                    'status_id' => $user['status_id'],
                    'user_code' => $user['user_code'],
                    'username' => $user['username'],
                    'password' => $user['password'],
                    'full_name' => $user['full_name'],
                    'gender' => $user['gender']
                ]
            );
        }
    }
}
