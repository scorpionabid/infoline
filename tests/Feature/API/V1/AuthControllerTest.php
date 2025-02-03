<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Region;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestCase;

class AuthControllerTest extends ApiTestCase
{
    use WithFaker;

    private $region;
    private $sector;
    private $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Factory istifadə edərək test datalarını yaradırıq
        $this->region = Region::factory()->create([
            'name' => 'Test Region',
            'phone' => '+994501234567'
        ]);

        $this->sector = Sector::factory()->create([
            'name' => 'Test Sector',
            'region_id' => $this->region->id
        ]);

        $this->school = School::factory()->create([
            'name' => 'Test School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school@edu.gov.az',
            'sector_id' => $this->sector->id
        ]);
    }

    /** @test */
    public function user_can_register()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@edu.gov.az',
            'username' => 'johndoe',
            'password' => 'Password123',
            'utis_code' => '1000001',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => $this->region->id,
            'sector_id' => $this->sector->id,
            'school_id' => $this->school->id
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'token'
                ],
                'message'
            ]);
    }

    /** @test */
    public function user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name', 'last_name', 'email', 'username',
                'password', 'utis_code', 'user_type', 'region_id',
                'sector_id'
            ]);
    }

    /** @test */
    public function user_can_login(): void
    {
        // Test üçün istifadəçi yaradırıq
        $password = 'Password123';
        $userData = [
            'email' => 'test@example.com',
            'password' => Hash::make($password),
            'utis_code' => '1234567',
            'first_name' => 'Test',
            'last_name' => 'User',  
            'username' => 'testuser',
            'user_type' => UserType::SUPER_ADMIN->value
        ];
        
        $user = User::create($userData);

        // Login sorğusu göndəririk
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password // həşlənməmiş password göndəririk
        ]);

        // Response-u yoxlayırıq
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'username',
                        'utis_code',
                        'user_type',
                        'created_at',
                        'updated_at'
                    ],
                    'token'
                ],
                'message'
            ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@edu.gov.az',
            'password' => 'WrongPassword123'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = $this->createSchoolAdmin($this->region, $this->sector, $this->school);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /** @test */
    public function user_can_get_their_details()
    {
        $user = $this->createSchoolAdmin($this->region, $this->sector, $this->school);

        $response = $this->actingAsUser($user)
            ->getJson('/api/v1/auth/user');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'username',
                    'utis_code',
                    'user_type',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);
    }
}