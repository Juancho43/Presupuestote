<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost = $this->faker->randomFloat(2, 100, 10000);

        $profitPercentage = $this->faker->numberBetween(10, 200); // Random profit percentage between 10% and 50%
        $profit = $cost * ($profitPercentage / 100);
        $price = $cost + $profit;
        return [
        'made_date' => $this->faker->date(),
        'description' => $this->faker->text(20),
        'dead_line' => $this->faker->date(),
        'state' => 'Presupuestado',
        'cost' => $cost,
        'profit' => $profit,
        'price' => $price,
        'client_id' => Client::factory(),
        ];
    }
}
