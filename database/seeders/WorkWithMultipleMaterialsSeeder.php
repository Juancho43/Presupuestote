<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Material;
use App\Models\Measure;
use App\Models\Price;
use App\Models\Stock;
use App\Models\Work;
use Illuminate\Database\Seeder;

class WorkWithMultipleMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        // Create basic measures if they don't exist
        $measures = [
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Meter', 'abbreviation' => 'm'],
            ['name' => 'Square Meter', 'abbreviation' => 'm²'],
            ['name' => 'Unit', 'abbreviation' => 'u'],
            ['name' => 'Liter', 'abbreviation' => 'l'],
        ];

        foreach ($measures as $measure) {
            Measure::firstOrCreate($measure);
        }

        // Create budget
        $budget = Budget::create([
            'made_date' => now(),
            'description' => 'Construction Project XYZ',
            'dead_line' => now()->addMonths(3),
            'cost' => 0,
            'profit' => 2500,
            'price' => 0,
            'client_id' => 1, // Ensure this client exists
        ]);

        // Create work
        $work = Work::create([
            'order' => 1,
            'name' => 'Main Construction Phase',
            'notes' => 'Complete building structure and finishing',
            'estimated_time' => 160,
            'dead_line' => now()->addMonths(2),
            'cost' => 0,
            'budget_id' => $budget->id,
        ]);

        // Materials data with their specifications
        $materials = [
            [
                'name' => 'Portland Cement',
                'description' => 'High-quality construction cement',
                'brand' => 'CementCo',
                'measure_id' => 1, // kg
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 500,
                'price' => 0.15,
                'stock' => 1000,
            ],
            [
                'name' => 'Steel Rebar 12mm',
                'description' => 'Reinforcement steel bars',
                'brand' => 'SteelMaster',
                'measure_id' => 2, // m
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 200,
                'price' => 5.75,
                'stock' => 500,
            ],
            [
                'name' => 'Ceramic Tiles',
                'description' => 'Floor tiles 30x30cm',
                'brand' => 'TilePro',
                'measure_id' => 3, // m²
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 100,
                'price' => 12.50,
                'stock' => 250,
            ],
            [
                'name' => 'Window Frame',
                'description' => 'Aluminum window frame 1x1.2m',
                'brand' => 'WindowTech',
                'measure_id' => 4, // unit
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 8,
                'price' => 75.00,
                'stock' => 15,
            ],
            [
                'name' => 'Paint',
                'description' => 'Interior wall paint',
                'brand' => 'ColorMax',
                'measure_id' => 5, // l
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 50,
                'price' => 8.25,
                'stock' => 200,
            ],
            [
                'name' => 'Concrete Blocks',
                'description' => '20x20x40cm construction blocks',
                'brand' => 'BlockMaster',
                'measure_id' => 4, // unit
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 1000,
                'price' => 2.15,
                'stock' => 2000,
            ],
            [
                'name' => 'Sand',
                'description' => 'Fine construction sand',
                'brand' => 'Generic',
                'measure_id' => 1, // kg
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 2000,
                'price' => 0.05,
                'stock' => 5000,
            ],
            [
                'name' => 'Gravel',
                'description' => 'Construction gravel 20mm',
                'brand' => 'Generic',
                'measure_id' => 1, // kg
                'unit_measure' => 1.00, // Assuming unit measure is in cubic meters
                'quantity' => 1500,
                'price' => 0.08,
                'stock' => 4000,
            ],
        ];

        foreach ($materials as $materialData) {
            $material = Material::create([
                'name' => $materialData['name'],
                'description' => $materialData['description'],
                'color' => 'Standard',
                'brand' => $materialData['brand'],
                'sub_category_id' => 1, // Ensure this category exists
                'measure_id' => $materialData['measure_id'],
                'unit_measure' => $materialData['unit_measure'],
            ]);

            $price = Price::create([
                'price' => $materialData['price'],
                'date' => now(),
                'material_id' => $material->id,
            ]);

            $stock = Stock::create([
                'stock' => $materialData['stock'],
                'date' => now(),
                'material_id' => $material->id,
            ]);

            // Attach material to work
            $work->materials()->attach($material->id, [
                'quantity' => $materialData['quantity'],
                'price_id' => $price->id,
                'stock_id' => $stock->id,
            ]);
        }

        // Update costs
        $work->updateCost();
        $budget->updatePrice();
    }
}
