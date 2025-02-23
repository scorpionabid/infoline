<?php

namespace Database\Seeders;

use App\Domain\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@edu.az',
            'utis_code' => '123456',
            'username' => 'superadmin',
            'password' => Hash::make('Admin123!'),

            'user_type' => 'superadmin',
            'status' => true,
            'is_active' => true
        ]);
    }
}
