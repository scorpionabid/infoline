<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleControllerTest extends TestCase
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
    public function authenticated_user_can_get_all_roles()
    {
        Role::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/roles');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'is_system',
                        'permissions'
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function authenticated_user_can_create_role()
    {
        $roleData = [
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'Test Description'
        ];

        $response = $this->postJson('/api/v1/roles', $roleData);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'is_system',
                    'permissions'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('roles', $roleData);
    }

    /** @test */
    public function authenticated_user_can_update_role()
    {
        $role = Role::factory()->create();

        $updateData = [
            'name' => 'Updated Role',
            'slug' => 'updated-role',
            'description' => 'Updated Description'
        ];

        $response = $this->putJson("/api/v1/roles/{$role->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Role',
                    'slug' => 'updated-role'
                ]
            ]);

        $this->assertDatabaseHas('roles', $updateData);
    }

    /** @test */
    public function system_roles_cannot_be_deleted()
    {
        $systemRole = Role::factory()->state(['is_system' => true])->create();

        $response = $this->deleteJson("/api/v1/roles/{$systemRole->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Sistem rolları silinə bilməz'
            ]);
    }

    /** @test */
    public function non_system_roles_can_be_deleted()
    {
        $role = Role::factory()->create();

        $response = $this->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Rol uğurla silindi'
            ]);

        $this->assertSoftDeleted('roles', ['id' => $role->id]);
    }
}