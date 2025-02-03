<?php

namespace Tests\Unit;

use App\Domain\Entities\User;
use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Domain\Enums\UserType;
use App\Application\DTOs\UserDTO;
use App\Application\Services\UserService;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
    }

    public function test_can_create_super_admin()
    {
        $userData = [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'utis_code' => '1000001',
            'email' => 'super@admin.com',
            'username' => 'superadmin1',
            'password' => 'Password123',
            'user_type' => UserType::SUPER_ADMIN->value
        ];

        $userDTO = new UserDTO($userData);
        $user = $this->userService->create($userDTO);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['first_name'], $user->first_name);
        $this->assertEquals($userData['last_name'], $user->last_name);
        $this->assertEquals($userData['utis_code'], $user->utis_code);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['username'], $user->username);
        $this->assertEquals($userData['user_type'], $user->user_type->value);
        $this->assertTrue($user->isSuperAdmin());
    }

    public function test_can_create_sector_admin()
    {
        // First create a region
        $region = Region::create([
            'name' => 'Test Region',
            'phone' => '+994501234567'
        ]);

        $userData = [
            'first_name' => 'Sector',
            'last_name' => 'Admin',
            'utis_code' => '1000002',
            'email' => 'sector@admin.com',
            'username' => 'sectoradmin1',
            'password' => 'Password123',
            'user_type' => UserType::SECTOR_ADMIN->value,
            'region_id' => $region->id
        ];

        $userDTO = new UserDTO($userData);
        $user = $this->userService->create($userDTO);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['region_id'], $user->region_id);
        $this->assertTrue($user->isSectorAdmin());
    }

    public function test_can_create_school_admin()
    {
        // Create necessary relationships
        $region = Region::create([
            'name' => 'Test Region',
            'phone' => '+994501234567'
        ]);
        
        $sector = Sector::create([
            'name' => 'Test Sector',
            'phone' => '+994501234567',
            'region_id' => $region->id
        ]);
        
        $school = School::create([
            'name' => 'Test School',
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'test@school.edu.az',
            'sector_id' => $sector->id
        ]);

        $userData = [
            'first_name' => 'School',
            'last_name' => 'Admin',
            'utis_code' => '1000003',
            'email' => 'school@admin.com',
            'username' => 'schooladmin1',
            'password' => 'Password123',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => $region->id,
            'sector_id' => $sector->id,
            'school_id' => $school->id
        ];

        $userDTO = new UserDTO($userData);
        $user = $this->userService->create($userDTO);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['region_id'], $user->region_id);
        $this->assertEquals($userData['sector_id'], $user->sector_id);
        $this->assertEquals($userData['school_id'], $user->school_id);
        $this->assertTrue($user->isSchoolAdmin());
    }

    public function test_validate_utis_code_format()
    {
        $userData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'utis_code' => '123', // Invalid UTIS code
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'Password123',
            'user_type' => UserType::SUPER_ADMIN->value
        ];

        $userDTO = new UserDTO($userData);
        $errors = $userDTO->validate();

        $this->assertArrayHasKey('utis_code', $errors);
    }

    public function test_validate_password_complexity()
    {
        $userData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'utis_code' => '1000004',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'simple', // Invalid password
            'user_type' => UserType::SUPER_ADMIN->value
        ];

        $userDTO = new UserDTO($userData);
        $errors = $userDTO->validate();

        $this->assertArrayHasKey('password', $errors);
    }

    public function test_validate_unique_fields()
    {
        // Create first user
        $userData1 = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'utis_code' => '1000005',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'Password123',
            'user_type' => UserType::SUPER_ADMIN->value
        ];

        $userDTO1 = new UserDTO($userData1);
        $this->userService->create($userDTO1);

        // Try to create second user with same unique fields
        $userData2 = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'utis_code' => '1000005', // Same UTIS code
            'email' => 'test@example.com', // Same email
            'username' => 'testuser', // Same username
            'password' => 'Password123',
            'user_type' => UserType::SUPER_ADMIN->value
        ];

        $userDTO2 = new UserDTO($userData2);

        $this->expectException(\InvalidArgumentException::class);
        $this->userService->create($userDTO2);
    }
}
