<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Region;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestCase;

class RegionControllerTest extends ApiTestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a super admin user
        $this->user = $this->createSuperAdmin();
    }

    /** @test */
    public function authenticated_user_can_get_all_regions()
    {
        Region::create([
            'name' => 'Test Region 1',
            'phone' => '+994501234567'
        ]);

        Region::create([
            'name' => 'Test Region 2',
            'phone' => '+994501234568'
        ]);

        $response = $this->actingAsUser()
            ->getJson('/api/v1/regions');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'phone',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'message'
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function authenticated_user_can_create_region()
    {
        $regionData = [
            'name' => 'New Region',
            'phone' => '+994501234567'
        ];

        $response = $this->actingAsUser()
            ->postJson('/api/v1/regions', $regionData);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'phone',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('regions', $regionData);
    }

    /** @test */
    public function authenticated_user_can_view_region()
    {
        $region = Region::create([
            'name' => 'Test Region',
            'phone' => '+994501234567'
        ]);

        $response = $this->actingAsUser()
            ->getJson("/api/v1/regions/{$region->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'phone',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);
    }

    /** @test */
    public function authenticated_user_can_update_region()
    {
        $region = Region::create([
            'name' => 'Test Region',
            'phone' => '+994501234567'
        ]);

        $updateData = [
            'name' => 'Updated Region',
            'phone' => '+994501234568'
        ];

        $response = $this->actingAsUser()
            ->putJson("/api/v1/regions/{$region->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'phone',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('regions', $updateData);
    }

    /** @test */
    public function authenticated_user_can_delete_region()
    {
        $region = Region::create([
            'name' => 'Test Region',
            'phone' => '+994501234567'
        ]);

        $response = $this->actingAsUser()
            ->deleteJson("/api/v1/regions/{$region->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Region deleted successfully'
            ]);

        $this->assertSoftDeleted('regions', ['id' => $region->id]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_regions()
    {
        $response = $this->getJson('/api/v1/regions');

        $response->assertUnauthorized();
    }

    /** @test */
    public function region_requires_valid_data()
    {
        $response = $this->actingAsUser()
            ->postJson('/api/v1/regions', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone']);
    }

    /** @test */
    public function region_phone_must_be_valid_format()
    {
        $response = $this->actingAsUser()
            ->postJson('/api/v1/regions', [
                'name' => 'Test Region',
                'phone' => 'invalid-phone'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }
}
