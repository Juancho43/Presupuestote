<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use App\Models\Price;
use App\Models\Stock;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class MaterialWithStockAndPriceSeeder extends Seeder
{
    public function run(): void
    {
        // Create a category and subcategory for the material
        $category = Category::factory()->create([
            'name' => 'Construction Materials',
        ]);

        $subCategory = SubCategory::factory()->create([
            'name' => 'Metals',
            'category_id' => $category->id,
        ]);

        // Create material with subcategory
        $material = Material::factory()->create([
            'name' => 'Premium Steel',
            'description' => 'High-quality construction steel',
            'color' => 'Silver',
            'brand' => 'SteelMaster',
            'sub_category_id' => $subCategory->id,
        ]);

        // Create two prices for the material
        $oldPrice = Price::factory()->create([
            'material_id' => $material->id,
            'price' => 75.50,
            'date' => now()->subMonth(),
        ]);

        $currentPrice = Price::factory()->create([
            'material_id' => $material->id,
            'price' => 82.99,
            'date' => now(),
        ]);

        // Create two stock records
        $oldStock = Stock::factory()->create([
            'material_id' => $material->id,
            'stock' => 150.00,
            'date' => now()->subMonth(),
        ]);

        $currentStock = Stock::factory()->create([
            'material_id' => $material->id,
            'stock' => 200.00,
            'date' => now(),
        ]);

        // Create additional materials with prices and stocks
        $material2 = Material::factory()->create([
            'name' => 'Aluminum Sheets',
            'description' => 'Lightweight aluminum sheets for construction',
            'color' => 'Silver',
            'brand' => 'MetalPro',
            'sub_category_id' => $subCategory->id,
        ]);

        Price::factory()->create([
            'material_id' => $material2->id,
            'price' => 45.75,
            'date' => now(),
        ]);

        Stock::factory()->create([
            'material_id' => $material2->id,
            'stock' => 120.00,
            'date' => now(),
        ]);
    }
}
