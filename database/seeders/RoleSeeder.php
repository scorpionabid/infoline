<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\Role;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'superadmin',
            'slug' => 'superadmin',
            'description' => 'Super Administrator',
            'is_system' => true
        ]);

        Role::create([
            'name' => 'sector-admin',
            'slug' => 'sector-admin',
            'description' => 'Sector Administrator',
            'is_system' => true
        ]);

        Role::create([
            'name' => 'school-admin',
            'slug' => 'school-admin',
            'description' => 'School Administrator',
            'is_system' => true
        ]);
    }
}