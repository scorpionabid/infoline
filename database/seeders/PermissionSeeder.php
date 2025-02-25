<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Management
        $this->createPermission('view-users');
        $this->createPermission('create-users');
        $this->createPermission('edit-users');
        $this->createPermission('delete-users');

        // Role Management
        $this->createPermission('view-roles');
        $this->createPermission('create-roles');
        $this->createPermission('edit-roles');
        $this->createPermission('delete-roles');

        // Region Management
        $this->createPermission('view-regions');
        $this->createPermission('create-regions');
        $this->createPermission('edit-regions');
        $this->createPermission('delete-regions');

        // Sector Management
        $this->createPermission('view-sectors');
        $this->createPermission('create-sectors');
        $this->createPermission('edit-sectors');
        $this->createPermission('delete-sectors');

        // School Management
        $this->createPermission('view-schools');
        $this->createPermission('create-schools');
        $this->createPermission('edit-schools');
        $this->createPermission('delete-schools');

        // Category Management
        $this->createPermission('view-categories');
        $this->createPermission('create-categories');
        $this->createPermission('edit-categories');
        $this->createPermission('delete-categories');
    }

    /**
     * Create a new permission
     */
    private function createPermission(string $name): void
    {
        Permission::create(['name' => $name, 'guard_name' => 'web']);
    }
}