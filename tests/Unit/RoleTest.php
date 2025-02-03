<?php

namespace Tests\Unit;

use App\Domain\Entities\Role;
use App\Domain\Entities\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_role()
    {
        $data = [
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'Test role description',
            'is_system' => false
        ];

        $role = Role::create($data);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals($data['name'], $role->name);
        $this->assertEquals($data['slug'], $role->slug);
    }

    /** @test */
    public function it_can_assign_permission_to_role()
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'Test role description'
        ]);

        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test-permission',
            'description' => 'Test permission'
        ]);

        $role->givePermissionTo($permission);

        $this->assertTrue($role->hasPermission('test-permission'));
    }

    /** @test */
    public function it_can_remove_permission_from_role()
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role'
        ]);

        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test-permission'
        ]);

        $role->givePermissionTo($permission);
        $this->assertTrue($role->hasPermission('test-permission'));

        $role->revokePermissionTo($permission);
        $this->assertFalse($role->hasPermission('test-permission'));
    }
}