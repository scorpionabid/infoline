<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing roles and permissions
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create roles
        $superAdmin = Role::create([
            'name' => 'super',
            'guard_name' => 'web',
            'description' => 'Tam səlahiyyətli admin',
            'is_system' => true
        ]);
        
        $sectorAdmin = Role::create([
            'name' => 'sector',
            'guard_name' => 'web',
            'description' => 'Sektor admin',
            'is_system' => true
        ]);
        
        $schoolAdmin = Role::create([
            'name' => 'school',
            'guard_name' => 'web',
            'description' => 'Məktəb admin',
            'is_system' => true
        ]);

        // Create permissions
        $manageSchools = Permission::create(['name' => 'manage-schools', 'guard_name' => 'web']);
        $assignAdmin = Permission::create(['name' => 'assign-admin', 'guard_name' => 'web']);
        $manageSchoolData = Permission::create(['name' => 'manage-school-data', 'guard_name' => 'web']);
        $viewSchoolData = Permission::create(['name' => 'view-school-data', 'guard_name' => 'web']);
        $manageRegions = Permission::create(['name' => 'manage-regions', 'guard_name' => 'web']);
        $manageSectors = Permission::create(['name' => 'manage-sectors', 'guard_name' => 'web']);
        $manageUsers = Permission::create(['name' => 'manage-users', 'guard_name' => 'web']);
        $viewReports = Permission::create(['name' => 'view-reports', 'guard_name' => 'web']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo([
            $manageSchools,
            $assignAdmin,
            $manageSchoolData,
            $viewSchoolData,
            $manageRegions,
            $manageSectors,
            $manageUsers,
            $viewReports
        ]);

        $sectorAdmin->givePermissionTo([
            $manageSchools,
            $assignAdmin,
            $viewReports
        ]);

        $schoolAdmin->givePermissionTo([
            $manageSchoolData,
            $viewSchoolData
        ]);
    }
}