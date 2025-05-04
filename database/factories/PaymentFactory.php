<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Salary;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invoice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      return [
          'amount' => $this->faker->randomFloat(2, 10, 1000),
          'date' => $this->faker->dateTime(),
          'description' => $this->faker->optional()->sentence(),

          'payable_type' => $this->faker->randomElement([Budget::class, Invoice::class, Salary::class]),
          'payable_id' => function (array $attributes) {
              // Get a real ID based on the payable_type
              $model = $attributes['payable_type'];
              return $model::factory()->create()->id;
          },
      ];
    }
}
