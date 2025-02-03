<?php

namespace Database\Factories;

use App\Domain\Entities\ColumnChoice;
use App\Domain\Entities\Column;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColumnChoiceFactory extends Factory
{
    protected $model = ColumnChoice::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->word(),
            'column_id' => Column::factory()
        ];
    }
}
