<?php

namespace Database\Factories;

use App\Domain\Entities\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'phone' => '+994' . $this->faker->numberBetween(50, 99) . $this->faker->numerify('#######')
        ];
    }
}