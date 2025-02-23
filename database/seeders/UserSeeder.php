<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin user
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@edu.az',
            'username' => 'superadmin',
            'password' => Hash::make('Admin123!'),
            'user_type' => UserType::SUPER_ADMIN,
            'utis_code' => 'SA0001', // Unique UTIS code for superadmin
            'email_verified_at' => now()
        ]);
    }
}