<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;

class SectorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $superadminRole = Role::where('name', 'super')->first();
        $sectorAdminRole = Role::where('name', 'sector')->first();
        $schoolAdminRole = Role::where('name', 'school')->first();

        // Get all permissions
        $permissions = Permission::all();

        // Assign all permissions to superadmin role
        $superadminRole->permissions()->sync($permissions->pluck('id'));

        // Assign specific permissions to sector admin
        $sectorAdminPermissions = Permission::whereIn('name', [
            'view-users', 'create-users', 'edit-users',
            'view-schools', 'create-schools', 'edit-schools',
            'view-categories'
        ])->get();
        $sectorAdminRole->permissions()->sync($sectorAdminPermissions->pluck('id'));

        // Assign specific permissions to school admin
        $schoolAdminPermissions = Permission::whereIn('name', [
            'view-categories'
        ])->get();
        $schoolAdminRole->permissions()->sync($schoolAdminPermissions->pluck('id'));

        // Assign superadmin role to superadmin user
        $superadminUser = User::where('user_type', UserType::SUPER_ADMIN)->first();
        $superadminUser->assignRole('super');
    }
}
