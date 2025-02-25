<?php

namespace Database\Factories;

use App\Domain\Entities\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
   protected $model = Role::class;

   public function definition(): array
   {
       $name = $this->faker->unique()->word();
       
       return [
           'name' => $name,
           'guard_name' => 'web',
           'description' => $this->faker->sentence(),
           'is_system' => false // default olaraq system rolu deyil
       ];
   }

   // System rolları üçün state metodu
   public function system(): static
   {
       return $this->state(fn (array $attributes) => [
           'is_system' => true
       ]);
   }

   // SuperAdmin rolu üçün
   public function superAdmin(): static
   {
       return $this->state(fn (array $attributes) => [
           'name' => 'super',
           'guard_name' => 'web',
           'description' => 'Tam səlahiyyətli admin',
           'is_system' => true
       ]);
   }
}