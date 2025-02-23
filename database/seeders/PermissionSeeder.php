<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Management
        $this->createPermission('view-users', 'View users', 'user');
        $this->createPermission('create-users', 'Create users', 'user');
        $this->createPermission('edit-users', 'Edit users', 'user');
        $this->createPermission('delete-users', 'Delete users', 'user');

        // Role Management
        $this->createPermission('view-roles', 'View roles', 'role');
        $this->createPermission('create-roles', 'Create roles', 'role');
        $this->createPermission('edit-roles', 'Edit roles', 'role');
        $this->createPermission('delete-roles', 'Delete roles', 'role');

        // Region Management
        $this->createPermission('view-regions', 'View regions', 'region');
        $this->createPermission('create-regions', 'Create regions', 'region');
        $this->createPermission('edit-regions', 'Edit regions', 'region');
        $this->createPermission('delete-regions', 'Delete regions', 'region');

        // Sector Management
        $this->createPermission('view-sectors', 'View sectors', 'sector');
        $this->createPermission('create-sectors', 'Create sectors', 'sector');
        $this->createPermission('edit-sectors', 'Edit sectors', 'sector');
        $this->createPermission('delete-sectors', 'Delete sectors', 'sector');

        // School Management
        $this->createPermission('view-schools', 'View schools', 'school');
        $this->createPermission('create-schools', 'Create schools', 'school');
        $this->createPermission('edit-schools', 'Edit schools', 'school');
        $this->createPermission('delete-schools', 'Delete schools', 'school');

        // Category Management
        $this->createPermission('view-categories', 'View categories', 'category');
        $this->createPermission('create-categories', 'Create categories', 'category');
        $this->createPermission('edit-categories', 'Edit categories', 'category');
        $this->createPermission('delete-categories', 'Delete categories', 'category');
    }

    /**
     * Create a new permission
     */
    private function createPermission(string $name, string $description, string $group): void
    {
        Permission::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $description,
            'group' => $group
        ]);
    }
}