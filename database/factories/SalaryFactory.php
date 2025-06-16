<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salary>
 */
class SalaryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->text(20),
            'amount' => fake()->randomFloat(2, 100, 10000),
            'date' => fake()->date(),
            'active' => fake()->boolean(),
            'employee_id' => Employee::factory()
        ];
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'active' => false
        ]);
    }

    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'active' => true
        ]);
    }
}
