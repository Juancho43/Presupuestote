<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Client;
use App\Models\Material;
use App\Models\Payment;
use App\Models\Person;
use App\Models\Work;
use App\States\BudgetState\BudgetState;
use App\States\PaymentState\PaymentState;
use App\States\WorkState\WorkState;
use Illuminate\Database\Seeder;

class ClientWithBudgetSeeder extends Seeder
{
    public function run(): void
    {
        // Create clients
        $clients = [];
        for ($i = 1; $i <= 3; $i++) {
            $person = Person::factory()->create([
                'name' => "Client {$i}",
                'last_name' => "LastName {$i}",
                'dni' => "1234567{$i}",
                'phone_number' => "123-456-78{$i}",
                'mail' => "client{$i}@example.com"
            ]);

            $clients[] = Client::factory()->create([
                'person_id' => $person->id,
                'balance' => 0
            ]);
        }

        // Get some existing materials
        $materials = Material::with(['latestPrice', 'latestStock'])->take(5)->get();

        foreach ($clients as $client) {
            // Create multiple budgets for each client
            for ($i = 1; $i <= 2; $i++) {
                $budget = Budget::factory()->create([
                    'client_id' => $client->id,
                    'made_date' => now(),
                    'dead_line' => now()->addMonths(2),
                    'description' => "Budget {$i} for {$client->person->name}",

                    'cost' => 0,
                    'profit' => 1000,
                    'price' => 0
                ]);

                // Create works for each budget
                for ($j = 1; $j <= 3; $j++) {
                    $work = Work::factory()->create([
                        'budget_id' => $budget->id,
                        'order' => $j,
                        'name' => "Work {$j} for Budget {$i}",
                        'estimated_time' => rand(1, 10),
                        'dead_line' => now()->addDays(rand(5, 30)),
                        'cost' => 0
                    ]);

                    // Attach materials to work
                    foreach ($materials->random(2) as $material) {
                        $quantity = rand(1, 10);
                        $work->materials()->attach($material->id, [
                            'quantity' => $quantity,
                            'price_id' => $material->latestPrice->id,
                            'stock_id' => $material->latestStock->id
                        ]);
                    }

                    $work->updateCost();
                }

                $budget->updatePrice();

            }

            $client->updateBalance();
        }
    }
}
