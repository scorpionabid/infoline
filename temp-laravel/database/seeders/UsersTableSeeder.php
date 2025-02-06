<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // SuperAdmin
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@infoline.edu.az',
            'username' => 'superadmin',
            'password' => bcrypt('Admin123!'),
            'utis_code' => '1111111',
            'user_type' => UserType::SUPER_ADMIN->value
        ]);

        // Sektor Admini
        User::create([
            'first_name' => 'Sektor',
            'last_name' => 'Admin',
            'email' => 'sektoradmin@infoline.edu.az',
            'username' => 'sektoradmin',
            'password' => bcrypt('Admin123!'),
            'utis_code' => '2222222',
            'user_type' => UserType::SECTOR_ADMIN->value,
            'region_id' => 1,
            'sector_id' => 1
        ]);

        // Məktəb Admini 
        User::create([
            'first_name' => 'Məktəb',
            'last_name' => 'Admin',
            'email' => 'mekteadmin@infoline.edu.az', 
            'username' => 'mektebadmin',
            'password' => bcrypt('Admin123!'),
            'utis_code' => '3333333',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => 1,
            'sector_id' => 1,
            'school_id' => 1
        ]);
    }
}