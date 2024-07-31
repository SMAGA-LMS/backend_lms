<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = DB::select(
            'SELECT users.id, users.gender
            FROM users
            INNER JOIN roles
            ON users.role_id = roles.id
            WHERE roles.name = ?',
            ['ADMIN']
        );

        foreach ($admins as $admin) {
            // if ($admin->gender === 'MALE') {
            //     $prefix = 'AM';
            //     $adminCode = $prefix . $amCounter++;
            // } else {
            //     $prefix = 'AF';
            //     $adminCode = $prefix . $afCounter++;
            // }
            DB::insert(
                'INSERT INTO admins (user_id, created_at)
                VALUES (:user_id, :created_at)',
                [
                    'user_id' => $admin->id,
                    'created_at' => Carbon::now()
                ]
            );
        }
    }
}
