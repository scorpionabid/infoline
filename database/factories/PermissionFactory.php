<?php

namespace Database\Factories;

use App\Domain\Entities\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'group' => $this->faker->randomElement([
                'user-management',
                'school-management',
                'sector-management',
                'region-management',
                'report-management'
            ])
        ];
    }

    /**
     * User idarəetməsi üçün icazə yaradır
     */
    public function userManagement(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'group' => 'user-management'
            ];
        });
    }

    /**
     * Məktəb idarəetməsi üçün icazə yaradır
     */
    public function schoolManagement(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'group' => 'school-management'
            ];
        });
    }
}