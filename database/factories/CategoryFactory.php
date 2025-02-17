<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Entities\Category;
use App\Domain\Entities\Column;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word()
        ];
    }

    public function hasColumns(int $count = 1)
    {
        return $this->afterCreating(function (Category $category) use ($count) {
            Column::factory()
                ->count($count)
                ->create([
                    'category_id' => $category->id
                ]);
        });
    }
}