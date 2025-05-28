<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Material;
use App\Models\Measure;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MaterialControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_materials_list(): void
    {
        Material::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/materials');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'color',
                        'brand',
                        'measure',
                        'subcategory',
                        'latestStock',
                        'latestPrice'
                    ]
                ]
            ]);
    }

    public function test_show_returns_material_information()
    {
        $material = Material::factory()->create();

        $response = $this->getJson("/api/v1/materials/{$material->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'color',
                    'brand',
                    'measure',
                    'subcategory'
                ]
            ]);
    }

    public function test_store_creates_new_material()
    {
        $subCategory = SubCategory::factory()->create();
        $measure = Measure::factory()->create();
        $data = [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'brand' => $this->faker->word(),
            'color' => $this->faker->colorName(),
            'measure_id' => $measure->id, // Assuming a measure with ID 1 exists
            'sub_category_id' =>$subCategory->id, // Assuming a subcategory with ID 1 exists
        ];

        $response = $this->postJson('/api/v1/materials', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'color',
                    'brand',
                ]
            ]);

        $this->assertDatabaseHas('materials', [
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

    }

    public function test_update_updates_existing_material()
    {
        $material = Material::factory()->create();

        $subCategory = SubCategory::factory()->create();
        $measure = Measure::factory()->create();
        $data = [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'brand' => $this->faker->word(),
            'color' => $this->faker->colorName(),
            'measure_id' => $measure->id, // Assuming a measure with ID 1 exists
            'sub_category_id' =>$subCategory->id, // Assuming a subcategory with ID 1 exists
        ];

        $response = $this->putJson("/api/v1/materials/{$material->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'color',
                    'brand',
                ]
            ]);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'color' => $data['color'],
            'brand' => $data['brand']
        ]);
    }

    public function test_delete_should_not_return_material()
    {
        $material = Material::factory()->create();

        $response = $this->deleteJson("/api/v1/materials/{$material->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/materials/{$material->id}");
        $response->assertStatus(500)
            ->assertJson([
                'message' => "Service Error: can't find Material"
            ]);
    }




}
