<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\User;
use Spatie\Permission\Models\{Role, Permission};
use App\Domain\Enums\UserType;

class SuperAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Get superadmin user
        $superadmin = User::where('user_type', UserType::SUPER_ADMIN->value)->first();
        
        if (!$superadmin) {
            return;
        }

        // Get superadmin role
        $role = Role::where('name', 'super')->first();
        
        if (!$role) {
            return;
        }

        // Get all permissions
        $permissions = Permission::all();

        // Assign all permissions to superadmin role
        $role->permissions()->sync($permissions->pluck('id'));

        // Assign role to superadmin
        $superadmin->assignRole('super');
    }
}
