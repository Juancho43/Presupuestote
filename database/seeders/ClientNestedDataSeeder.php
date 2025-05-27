<?php

namespace Database\Seeders;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Client;
use App\Models\Material;
use App\Models\Payment;
use App\Models\Person;
use App\Models\Price;
use App\Models\Stock;
use App\Models\SubCategory;
use App\Models\Work;
use Illuminate\Database\Seeder;

class ClientNestedDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create a person
        $person = Person::factory()->create([
            'name' => 'Mauro',
            'last_name' => 'Doe',
            'phone_number' => '1234567890',
            'mail' => 'Mauro@example.com',
            'dni' => '555333222',
        ]);

        // Create a client associated with the person
        $client = Client::factory()->create([
            'person_id' => $person->id,
            'balance' => 0.00, // Initial balance
        ]);

        // Create a category and subcategory
        $category = Category::factory()->create([
            'name' => 'Construction Materials',
        ]);

        $subCategory = SubCategory::factory()->create([
            'name' => 'Wood',
            'category_id' => $category->id,
        ]);

        // Create a material with price and stock
        $material = Material::factory()->create([
            'name' => 'Pine Wood',
            'description' => 'High quality pine wood',
            'subcategory_id' => $subCategory->id,
        ]);

        // Create price for material
        $price = Price::factory()->create([
            'material_id' => $material->id,
            'price' => 150.50,
            'date' => now(),
        ]);

        // Create stock for material
        $stock = Stock::factory()->create([
            'material_id' => $material->id,
            'stock' => 100,
            'date' => now(),
        ]);
        $cost = $price->price * 5; // Assuming 5 units of material
        $profit = $cost * 0.2; // Assuming 20% profit
        $budgetPrice = $cost + $profit;
        // Create a budget with works and payments
        $budget = Budget::factory()->create([
            'client_id' => $client->id,
            'made_date' => now(),
            'description' => 'Kitchen renovation',
            'dead_line' => now()->addDays(30),
            'cost' => $cost,
            'profit' => $profit,
            'price' => $budgetPrice,
        ]);


        // Create work
        $work = Work::factory()->create([
            'budget_id' => $budget->id,
            'name' => 'Cabinet Installation',
            'estimated_time' => '8',
            'order' => 1,
            'cost' => $cost,
        ]);

        // Attach material to work with proper pivot data
        $work->materials()->attach($material->id, [
            'price_id' => $price->id,
            'stock_id' => $stock->id,
            'quantity' => 5
        ]);

        $work->updateCost();
        // Create payment for the budget
        Payment::factory()->create([
            'payable_type' => Budget::class,
            'payable_id' => $budget->id,
            'amount' => 10.00,
            'date' => now(),
            'description' => 'First payment',
        ]);
    }
}
