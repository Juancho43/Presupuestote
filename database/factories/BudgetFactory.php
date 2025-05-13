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
        return [

        'made_date' => $this->faker->date(),
        'description' => $this->faker->text(),
        'dead_line' => $this->faker->date(),
        'status' => $this->faker->randomElement(['Presupuestado', 'Aprobado', 'Rechazado', 'En proceso', 'Entregado', 'Cancelado']),
        'cost' => $this->faker->randomFloat(2, 100, 10000),
        'client_id' => Client::factory(),
        ];
    }
}
