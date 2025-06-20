<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    return [
        'name' => $this->faker->word(),
        'description' => $this->faker->sentence(),
        'color' => $this->faker->colorName(),
        'brand' => $this->faker->company(),
        'measure_id' => \App\Models\Measure::factory(),
        'sub_category_id' => \App\Models\SubCategory::factory(),
    ];

    }
}
