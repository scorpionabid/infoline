<?php

namespace Database\Factories;

use App\Domain\Entities\Sector;
use App\Domain\Entities\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectorFactory extends Factory
{
    protected $model = Sector::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'phone' => '+994' . $this->faker->numberBetween(50, 99) . $this->faker->numerify('#######'),
            'region_id' => Region::factory()->create()->id
        ];
    }
    public function forRegion(Region $region): Factory
    {
        return $this->state(function (array $attributes) use ($region) {
            return [
                'region_id' => $region->id,
            ];
        });
    }
}