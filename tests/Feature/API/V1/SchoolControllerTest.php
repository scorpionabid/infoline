<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Region;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestCase;

class SchoolControllerTest extends ApiTestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $region;
    protected $sector;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->region = $this->createRegion();
        $this->sector = $this->createSector($this->region);

        // Create a super admin user
        $this->user = $this->createSuperAdmin();
    }

    /** @test */
    public function authenticated_user_can_get_all_schools()
    {
        School::create([
            'name' => 'Test School 1',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school1@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        School::create([
            'name' => 'Test School 2',
            'utis_code' => '1234568',
            'phone' => '+994501234568',
            'email' => 'school2@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        $response = $this->actingAsUser()
            ->getJson('/api/v1/schools');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'utis_code',
                        'phone',
                        'email',
                        'sector_id',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'message'
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function authenticated_user_can_get_schools_by_sector()
    {
        School::create([
            'name' => 'Test School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        $response = $this->actingAsUser()
            ->getJson("/api/v1/sectors/{$this->sector->id}/schools");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'utis_code',
                        'phone',
                        'email',
                        'sector_id',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'message'
            ])
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function authenticated_user_can_create_school()
    {
        $schoolData = [
            'name' => 'New School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'newschool@edu.gov.az',
            'sector_id' => $this->sector->id
        ];

        $response = $this->actingAsUser()
            ->postJson('/api/v1/schools', $schoolData);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'utis_code',
                    'phone',
                    'email',
                    'sector_id',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('schools', $schoolData);
    }

    /** @test */
    public function authenticated_user_can_view_school()
    {
        $school = School::create([
            'name' => 'Test School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        $response = $this->actingAsUser()
            ->getJson("/api/v1/schools/{$school->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'utis_code',
                    'phone',
                    'email',
                    'sector_id',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);
    }

    /** @test */
    public function authenticated_user_can_update_school()
    {
        $school = School::create([
            'name' => 'Test School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        $updateData = [
            'name' => 'Updated School',
            'utis_code' => '1234568',
            'phone' => '+994501234568',
            'email' => 'updated@edu.gov.az',
            'sector_id' => $this->sector->id
        ];

        $response = $this->actingAsUser()
            ->putJson("/api/v1/schools/{$school->id}", $updateData);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'utis_code',
                    'phone',
                    'email',
                    'sector_id',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('schools', $updateData);
    }

    /** @test */
    public function authenticated_user_can_delete_school()
    {
        $school = School::create([
            'name' => 'Test School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        $response = $this->actingAsUser()
            ->deleteJson("/api/v1/schools/{$school->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'School deleted successfully'
            ]);

        $this->assertSoftDeleted('schools', ['id' => $school->id]);
    }

    /** @test */
    public function authenticated_user_can_view_school_admins()
    {
        $school = School::create([
            'name' => 'Test School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        User::create([
            'first_name' => 'School',
            'last_name' => 'Admin',
            'email' => 'schooladmin@edu.gov.az',
            'username' => 'schooladmin',
            'password' => bcrypt('Password123'),
            'utis_code' => '2000001',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => $this->region->id,
            'sector_id' => $this->sector->id,
            'school_id' => $school->id
        ]);

        $response = $this->actingAsUser()
            ->getJson("/api/v1/schools/{$school->id}/admins");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'username',
                        'utis_code',
                        'user_type',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'message'
            ])
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function school_requires_valid_data()
    {
        $response = $this->actingAsUser()
            ->postJson('/api/v1/schools', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'utis_code', 'phone', 'email', 'sector_id']);
    }

    /** @test */
    public function school_requires_valid_sector()
    {
        $response = $this->actingAsUser()
            ->postJson('/api/v1/schools', [
                'name' => 'Test School',
                'utis_code' => '1234567',
                'phone' => '+994501234567',
                'email' => 'school@edu.gov.az',
                'sector_id' => 999 // Non-existent sector ID
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sector_id']);
    }

    /** @test */
    public function school_requires_unique_utis_code()
    {
        School::create([
            'name' => 'Test School 1',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school1@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);

        $response = $this->actingAsUser()
            ->postJson('/api/v1/schools', [
                'name' => 'Test School 2',
                'utis_code' => '1234567', // Same UTIS code
                'phone' => '+994501234568',
                'email' => 'school2@edu.gov.az',
                'sector_id' => $this->sector->id
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['utis_code']);
    }
}
