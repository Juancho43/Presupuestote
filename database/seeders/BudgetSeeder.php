<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Budget;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there are clients in the database
        if (Client::count() === 0) {
            Client::factory(5)->create();
        }

        // Create 10 budgets, each associated with a random client
        Budget::factory(10)->create([
            'client_id' => Client::all()->random()->id,
        ]);
    }
}
