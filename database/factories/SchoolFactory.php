<?php

namespace Database\Factories;

use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition()
    {
        static $utisCode = 1000000;

        return [
            'name' => $this->faker->unique()->company() . ' Məktəbi',
            'utis_code' => (string)++$utisCode,
            'phone' => '+994' . $this->faker->numberBetween(50, 99) . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'sector_id' => Sector::factory(),
        ];
    }
}
