<?php

namespace Tests;

use App\Domain\Entities\Region;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail,
            'username' => fake()->unique()->userName,
            'password' => bcrypt('password'),
            'utis_code' => fake()->unique()->numerify('1######'),
            'user_type' => UserType::SUPER_ADMIN->value,
            'region_id' => null,
            'sector_id' => null,
            'school_id' => null
        ]);
    }

    protected function createSuperAdmin(): User
    {
        $password = 'password';
        $hashedPassword = bcrypt($password);

        \Log::info('Test login attempt', [
            'raw_password' => $password,
            'hashed_password' => $hashedPassword
        ]);

        return User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@edu.gov.az',
            'username' => 'superadmin',
            'password' => $password,
            'utis_code' => '1000001',
            'user_type' => UserType::SUPER_ADMIN->value,
            'region_id' => null,
            'sector_id' => null,
            'school_id' => null
        ]);
    }

    protected function createRegionAdmin(Region $region): User
    {
        return User::create([
            'first_name' => 'Region',
            'last_name' => 'Admin',
            'email' => 'regionadmin@edu.gov.az',
            'username' => 'regionadmin',
            'password' => bcrypt('Password123'),
            'utis_code' => '2000001',
            'user_type' => UserType::REGION_ADMIN->value,
            'region_id' => $region->id,
            'sector_id' => null,
            'school_id' => null
        ]);
    }

    protected function createSectorAdmin(Region $region, Sector $sector): User
    {
        return User::create([
            'first_name' => 'Sector',
            'last_name' => 'Admin',
            'email' => 'sectoradmin@edu.gov.az',
            'username' => 'sectoradmin',
            'password' => bcrypt('Password123'),
            'utis_code' => '3000001',
            'user_type' => UserType::SECTOR_ADMIN->value,
            'region_id' => $region->id,
            'sector_id' => $sector->id,
            'school_id' => null
        ]);
    }

    protected function createSchoolAdmin(Region $region, Sector $sector, School $school): User
    {
        return User::create([
            'first_name' => 'School',
            'last_name' => 'Admin',
            'email' => 'schooladmin@edu.gov.az',
            'username' => 'schooladmin',
            'password' => bcrypt('Password123'),
            'utis_code' => '4000001',
            'user_type' => UserType::SCHOOL_ADMIN->value,
            'region_id' => $region->id,
            'sector_id' => $sector->id,
            'school_id' => $school->id
        ]);
    }

    protected function createRegion(string $name = 'Test Region'): Region
    {
        return Region::factory()->create([
            'name' => $name,
            'phone' => '+994501234567'
        ]);
    }

    protected function createSector(Region $region, string $name = 'Test Sector'): Sector
    {
        return Sector::factory()->create([
            'name' => $name,
            'phone' => '+994501234567',
            'region_id' => $region->id
        ]);
    }

    protected function createSchool(Sector $sector, string $name = 'Test School'): School
    {
        return School::factory()->create([
            'name' => $name,
            'utis_code' => '1234567',
            'phone' => '+994501234567',
            'email' => 'school@edu.gov.az',
            'sector_id' => $sector->id
        ]);
    }

    protected function actingAsUser(User $user = null): self
    {
        $user = $user ?? $this->user;
        $token = $user->createToken('test-token')->plainTextToken;
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }
}
