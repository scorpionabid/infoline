<?php

namespace Database\Factories;

use App\Domain\Entities\User;
use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Domain\Enums\UserType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('password'),
            'utis_code' => fake()->unique()->numerify('1######'),
            'user_type' => UserType::SUPER_ADMIN->value,
            'region_id' => null,
            'sector_id' => null,
            'school_id' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => UserType::SUPER_ADMIN->value,
                'region_id' => null,
                'sector_id' => null,
                'school_id' => null,
            ];
        });
    }

    public function regionAdmin()
    {
        return $this->state(function (array $attributes) {
            $region = Region::factory()->create();
            return [
                'user_type' => UserType::REGION_ADMIN->value,
                'region_id' => $region->id,
                'sector_id' => null,
                'school_id' => null,
            ];
        });
    }

    public function sectorAdmin()
    {
        return $this->state(function (array $attributes) {
            $sector = Sector::factory()->create();
            return [
                'user_type' => UserType::SECTOR_ADMIN->value,
                'region_id' => $sector->region_id,
                'sector_id' => $sector->id,
                'school_id' => null,
            ];
        });
    }

    public function schoolAdmin()
    {
        return $this->state(function (array $attributes) {
            $school = School::factory()->create();
            return [
                'user_type' => UserType::SCHOOL_ADMIN->value,
                'region_id' => $school->sector->region_id,
                'sector_id' => $school->sector_id,
                'school_id' => $school->id,
            ];
        });
    }
}
