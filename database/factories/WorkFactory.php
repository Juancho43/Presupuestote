<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Work>
 */
class WorkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order' => $this->faker->numberBetween(1, 100),
            'name' => $this->faker->words(3, true),
            'notes' => $this->faker->optional()->sentence(),
            'estimated_time' => $this->faker->numberBetween(1, 100),
            'dead_line' => $this->faker->date(),
            'cost' => $this->faker->randomFloat(2, 100, 10000),
            'budget_id' => \App\Models\Budget::factory(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
