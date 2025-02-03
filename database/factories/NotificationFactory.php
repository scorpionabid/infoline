<?php

namespace Database\Factories;

use App\Domain\Entities\Notification;
use App\Domain\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['system', 'data_update', 'import', 'export']),
            'title' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'data' => ['test_key' => 'test_value'],
            'read_at' => null
        ];
    }

    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => now()
            ];
        });
    }
}