<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\User;
use App\Domain\Entities\Role;
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
        $superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super@edu.az',
            'password' => Hash::make('Admin123!'),
            'user_type' => UserType::SUPER_ADMIN->value,
            'utis_code' => '1000001', // Unique UTIS code for superadmin
            'email_verified_at' => now()
        ]);

        // Assign super admin role
        $superAdmin->assignRole('super');
    }
}