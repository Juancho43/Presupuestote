<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
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
            'last_name' => $this->faker->lastName(),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'mail' => $this->faker->unique()->safeEmail(),
            'dni' => $this->faker->unique()->numerify('########'),
            'cuit' => $this->faker->unique()->numerify('##-########-#'),
        ];
    }
}
