<?php

namespace Tests\Unit;

use App\Domain\Entities\Permission;
use App\Domain\Entities\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function it_can_create_permission()
   {
       $data = [
           'name' => 'Create School',
           'slug' => 'create-school',
           'description' => 'Can create new school',
           'group' => 'school-management'
       ];

       $permission = Permission::create($data);

       $this->assertInstanceOf(Permission::class, $permission);
       $this->assertEquals($data['name'], $permission->name);
       $this->assertEquals($data['slug'], $permission->slug);
       $this->assertEquals($data['group'], $permission->group);
   }

   /** @test */
   public function it_can_be_assigned_to_role()
   {
       $permission = Permission::create([
           'name' => 'Create School',
           'slug' => 'create-school',
           'group' => 'school-management'
       ]);

       $role = Role::create([
           'name' => 'School Manager',
           'slug' => 'school-manager'
       ]);

       $role->permissions()->attach($permission);

       $this->assertTrue($role->permissions->contains($permission));
       $this->assertTrue($permission->roles->contains($role));
   }

   /** @test */
   public function it_belongs_to_group()
   {
       $permission = Permission::create([
           'name' => 'Create School',
           'slug' => 'create-school',
           'group' => 'school-management'
       ]);

       $this->assertTrue($permission->isInGroup('school-management'));
       $this->assertFalse($permission->isInGroup('user-management'));
   }
}