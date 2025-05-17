<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Invoice;
use App\Models\Material;
use App\Models\Payment;
use App\Models\Person;
use App\Models\Price;
use App\Models\Stock;
use App\Models\SubCategory;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierNestedDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create a person for the supplier
        $person = Person::factory()->create([
            'name' => 'Robert',
            'last_name' => 'Smith',
            'phone_number' => '9876543210',
            'mail' => 'robert.smith@supplier.com',
            'dni' => '987654321',
        ]);

        // Create a supplier
        $supplier = Supplier::factory()->create([
            'person_id' => $person->id,
            'notes' => 'Reliable construction materials supplier',
            'balance' => 0,
        ]);

        // Create category and subcategory
        $category = Category::factory()->create([
            'name' => 'Building Materials',
        ]);

        $subCategory = SubCategory::factory()->create([
            'name' => 'Lumber',
            'category_id' => $category->id,
        ]);

        // Create materials with prices and stock
        $material1 = Material::factory()->create([
            'name' => 'Premium Pine Wood',
            'description' => 'High-grade pine lumber',
            'color' => 'Natural',
            'brand' => 'WoodCraft',
            'subcategory_id' => $subCategory->id,
        ]);

        $material2 = Material::factory()->create([
            'name' => 'Oak Planks',
            'description' => 'Premium oak planks',
            'color' => 'Brown',
            'brand' => 'WoodCraft',
            'subcategory_id' => $subCategory->id,
        ]);

        // Create prices for materials
        $price1 = Price::factory()->create([
            'material_id' => $material1->id,
            'price' => 45.99,
            'date' => now(),
        ]);

        $price2 = Price::factory()->create([
            'material_id' => $material2->id,
            'price' => 89.99,
            'date' => now(),
        ]);

        // Create stock records
        $stock1 = Stock::factory()->create([
            'material_id' => $material1->id,
            'stock' => 200,
            'date' => now(),
        ]);

        $stock2 = Stock::factory()->create([
            'material_id' => $material2->id,
            'stock' => 150,
            'date' => now(),
        ]);

        // Create invoice with materials
        $invoice = Invoice::factory()->create([
            'supplier_id' => $supplier->id,
            'date' => now(),
        ]);

        // Attach materials to invoice with proper pivot data including price_id and stock_id
        $invoice->materials()->attach($material1->id, [
            'price_id' => $price1->id,
            'stock_id' => $stock1->id,
            'quantity' => 50
        ]);

        $invoice->materials()->attach($material2->id, [
            'price_id' => $price2->id,
            'stock_id' => $stock2->id,
            'quantity' => 30
        ]);

        // Create payments for the invoice
        Payment::factory()->create([
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'amount' => 1500.00,
            'date' => now(),
            'description' => 'First payment for lumber order',
        ]);

        Payment::factory()->create([
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'amount' => 2000.00,
            'date' => now()->addDays(15),
            'description' => 'Final payment for lumber order',
        ]);
    }
}
