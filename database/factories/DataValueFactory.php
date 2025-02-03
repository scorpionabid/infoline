<?php

namespace Database\Factories;

use App\Domain\Entities\DataValue;
use App\Domain\Entities\Column;
use App\Domain\Entities\School;
use App\Domain\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataValueFactory extends Factory
{
    protected $model = DataValue::class;

    public function definition(): array
    {
        return [
            'column_id' => Column::factory(),
            'school_id' => School::factory(),
            'value' => $this->faker->sentence(),
            'status' => 'draft',
            'updated_by' => User::factory(),
            'comment' => null
        ];
    }

    /**
     * Status ilə yaratmaq üçün
     */
    public function status(string $status)
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status
            ];
        });
    }
}
