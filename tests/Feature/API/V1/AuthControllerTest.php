<?php

namespace Tests\Feature\API\V1;

use App\Domain\Enums\UserType;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\Hash;
use Tests\ApiTestCase;

class AuthControllerTest extends ApiTestCase
{
    private $region;
    private $sector;
    private $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Base test datalarını yaradaq
        $this->region = $this->createRegion('Test Region');
        $this->sector = $this->createSector($this->region, 'Test Sector');
        $this->school = $this->createSchool($this->sector, 'Test School');
    }

    /** @test */
    public function user_can_login_with_valid_credentials(): void
    {
        // Test üçün istifadəçi yaradaq
        $password = 'password';
        $user = $this->createSuperAdmin();

        \Log::info('Test login attempt', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Login sorğusu göndərək
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        // Debug üçün response-u log edək
        if ($response->status() !== 200) {
            \Log::error('Login test failed', [
                'status' => $response->status(),
                'content' => $response->getContent(),
                'test_password' => $password,
                'user_email' => $user->email
            ]);
        }

        // Cavabı yoxlayaq
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
            
        // Token yaradılmasını yoxlayaq
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class
        ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_password(): void 
    {
        $user = $this->createSuperAdmin();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'any_password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}