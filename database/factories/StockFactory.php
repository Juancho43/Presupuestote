<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

            return [
                'stock' => $this->faker->randomFloat(2, 0, 1000),
                'date' => $this->faker->date(),
                'material_id' => \App\Models\Material::factory(),
            ];

    }
}
