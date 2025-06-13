<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use App\Models\Measure;
use App\Models\Price;
use App\Models\Stock;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class ExtendedMaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Create measures
        $measures = [
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Square Meter', 'abbreviation' => 'm²'],
            ['name' => 'Linear Meter', 'abbreviation' => 'm'],
            ['name' => 'Unit', 'abbreviation' => 'u'],
            ['name' => 'Box', 'abbreviation' => 'box']
        ];

        $createdMeasures = [];
        foreach ($measures as $measure)
        {
            $createdMeasures[$measure['abbreviation']] = Measure::factory()->create($measure);
        }

        // Create categories with their materials
        $categories = [
        'Construction Materials' => [
            'Metals' => [
                ['name' => 'Steel Bars', 'measure' => 'kg', 'price' => 82.99, 'stock' => 200],
                ['name' => 'Aluminum Sheets', 'measure' => 'm²', 'price' => 45.75, 'stock' => 150],
                ['name' => 'Metal Profiles', 'measure' => 'm', 'price' => 35.50, 'stock' => 300]
            ],
            'Wood' => [
                ['name' => 'Pine Plywood', 'measure' => 'm²', 'price' => 28.99, 'stock' => 100],
                ['name' => 'Oak Boards', 'measure' => 'm²', 'price' => 65.50, 'stock' => 80]
            ]
        ],
        'Plumbing' => [
            'Pipes' => [
                ['name' => 'PVC Pipes', 'measure' => 'm', 'price' => 12.99, 'stock' => 400],
                ['name' => 'Copper Pipes', 'measure' => 'm', 'price' => 28.50, 'stock' => 250]
            ],
            'Fittings' => [
            ['name' => 'PVC Elbows', 'measure' => 'u', 'price' => 3.99, 'stock' => 1000],
            ['name' => 'Copper Joints', 'measure' => 'u', 'price' => 8.50, 'stock' => 500]
            ]
        ],
        'Electrical' => [
            'Cables' => [
                ['name' => 'Electric Cable 12AWG', 'measure' => 'm', 'price' => 4.99, 'stock' => 600],
                ['name' => 'Network Cable CAT6', 'measure' => 'box', 'price' => 89.99, 'stock' => 50]
            ],
            'Accessories' => [
                ['name' => 'Wall Sockets', 'measure' => 'u', 'price' => 5.99, 'stock' => 800],
                ['name' => 'Circuit Breakers', 'measure' => 'u', 'price' => 15.50, 'stock' => 300]
            ]
        ]
        ];

        foreach ($categories as $categoryName => $subcategories)
        {
            $category = Category::factory()->create(['name' => $categoryName]);

            foreach ($subcategories as $subcategoryName => $materials)
            {
                $subcategory = SubCategory::factory()->create([
                'name' => $subcategoryName,
                'category_id' => $category->id
                ]);

                foreach ($materials as $materialData)
                {
                    $material = Material::factory()->create([
                    'name' => $materialData['name'],
                    'description' => "High quality {$materialData['name']}",
                    'color' => 'Various',
                    'brand' => 'Generic',
                    'sub_category_id' => $subcategory->id,
                    'measure_id' => $createdMeasures[$materialData['measure']]->id,
                    'unit_measure' => 2,
                    ]);

                    Price::factory()->create([
                    'material_id' => $material->id,
                    'price' => $materialData['price'],
                    'date' => now()
                    ]);

                    Stock::factory()->create([
                    'material_id' => $material->id,
                    'stock' => $materialData['stock'],
                    'date' => now()
                    ]);
                }
            }
        }
    }
}
