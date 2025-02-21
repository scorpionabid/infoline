<?php

namespace Database\Seeders;

use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SuperAdmin rolunu yaradaq
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'description' => 'Tam səlahiyyətli admin',
                'is_system' => true
            ]
        );

        // Sector Admin rolunu yaradaq
        Role::firstOrCreate(
            ['slug' => 'sector-admin'],
            [
                'name' => 'Sector Admin',
                'guard_name' => 'web',
                'description' => 'Sektor üzrə məsul admin',
                'is_system' => true
            ]
        );

        // School Admin rolunu yaradaq
        Role::firstOrCreate(
            ['slug' => 'school-admin'],
            [
                'name' => 'School Admin',
                'guard_name' => 'web',
                'description' => 'Məktəb üzrə məsul admin',
                'is_system' => true
            ]
        );

        // SuperAdmin userə rolu təyin edək
        $superAdminUser = User::where('email', 'superadmin@example.com')->first();
        if ($superAdminUser) {
            DB::table('user_roles')->updateOrInsert(
                [
                    'user_id' => $superAdminUser->id,
                    'role_id' => $superAdmin->id
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            
            // Cache'i təmizləyək
            Cache::forget('user_roles_' . $superAdminUser->id);
        }
    }
}