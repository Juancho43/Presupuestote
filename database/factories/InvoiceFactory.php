<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    return [
        'description' => $this->faker->text(20),
        'date' => $this->faker->date(),
        'total' => 0,
        'supplier_id' => \App\Models\Supplier::factory(),
    ];
    }
}
