<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Permission;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use Database\Factories\PermissionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // SuperAdmin user yaradırıq
        $this->admin = User::factory()->create(['user_type' => 'superadmin']);
        
        // Sanctum authentication
        Sanctum::actingAs($this->admin);
    }

    /** @test */
    public function it_can_list_all_permissions()
    {
        // Test üçün permissions yaradırıq
        Permission::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/permissions');

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'group'
                        ]
                    ],
                    'message'
                ]);
    }

    /** @test */
    public function it_can_create_new_permission()
    {
        $data = [
            'name' => 'Create School',
            'slug' => 'create-school',
            'description' => 'Can create new school',
            'group' => 'school-management'
        ];

        $response = $this->postJson('/api/v1/permissions', $data);

        $response->assertCreated()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'group'
                    ],
                    'message'
                ]);

        $this->assertDatabaseHas('permissions', $data);
    }

    /** @test */
    public function it_can_show_permission()
    {
        $permission = Permission::factory()->create();

        $response = $this->getJson("/api/v1/permissions/{$permission->id}");

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'group'
                    ],
                    'message'
                ]);
    }

    /** @test */
    public function it_can_update_permission()
    {
        $permission = Permission::factory()->create();

        $data = [
            'name' => 'Updated Permission',
            'slug' => 'updated-permission',
            'description' => 'Updated description',
            'group' => 'updated-group'
        ];

        $response = $this->putJson("/api/v1/permissions/{$permission->id}", $data);

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'group'
                    ],
                    'message'
                ]);

        $this->assertDatabaseHas('permissions', $data);
    }

    /** @test */
    public function it_can_delete_permission()
    {
        $permission = Permission::factory()->create();

        $response = $this->deleteJson("/api/v1/permissions/{$permission->id}");

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data',
                    'message'
                ]);

        $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_permission()
    {
        $response = $this->postJson('/api/v1/permissions', []);

        $response->assertUnprocessable()
                ->assertJsonValidationErrors(['name', 'slug', 'group']);
    }

    /** @test */
    public function it_validates_unique_slug_when_creating_permission()
    {
        $existingPermission = Permission::factory()->create();

        $data = [
            'name' => 'New Permission',
            'slug' => $existingPermission->slug, // existing slug
            'description' => 'Some description',
            'group' => 'some-group'
        ];

        $response = $this->postJson('/api/v1/permissions', $data);

        $response->assertUnprocessable()
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function non_super_admin_cannot_manage_permissions()
    {
        // Normal user yaradırıq
        $user = User::factory()->create(['user_type' => 'schooladmin']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/permissions');

        $response->assertForbidden();
    }

    /** @test */
    public function it_can_assign_permission_to_role()
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();

        $response = $this->postJson("/api/v1/permissions/{$permission->id}/roles", [
            'role_id' => $role->id
        ]);

        $response->assertOk();
        
        $this->assertDatabaseHas('role_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
    }
}