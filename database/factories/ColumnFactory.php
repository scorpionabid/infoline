<?php

namespace Database\Factories;

use App\Domain\Entities\Column;
use App\Domain\Entities\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColumnFactory extends Factory
{
    protected $model = Column::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'data_type' => $this->faker->randomElement(['text', 'number', 'date', 'select', 'multiselect', 'file']),
            'end_date' => $this->faker->optional()->date(),
            'input_limit' => $this->faker->optional()->numberBetween(1, 1000),
            'category_id' => Category::factory()
        ];
    }
}
