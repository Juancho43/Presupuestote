<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Material;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PriceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_price_list(): void
    {
        Price::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/prices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'price',
                        'date',
                    ]
                ]
            ]);
    }

    public function test_show_returns_price_information()
    {
        $Price = Price::factory()->create();

        $response = $this->getJson("/api/v1/prices/{$Price->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'price',
                    'date',
                    'material'
                ]
            ]);
    }

    public function test_store_creates_new_price()
    {
        $material = Material::factory()->create();
        $data = [
            'price' => $this->faker->randomFloat(2, 1, 100),
            'date' => $this->faker->date(),
            'material_id' => $material->id,
        ];

        $response = $this->postJson('/api/v1/prices', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'price',
                    'date',
                ]
            ]);

        $this->assertDatabaseHas('prices', [
            'price' => $data['price'],
            'date' => $data['date'],
        ]);

    }

    public function test_update_updates_existing_price()
    {
        $material = Material::factory()->create();
        $price = Price::factory()->create();
        $data = [
            'price' => $this->faker->randomFloat(2, 1, 100),
            'date' => $this->faker->date(),
            'material_id' => $material->id,
        ];


        $response = $this->putJson("/api/v1/prices/{$price->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'price',
                    'date',
                ]
            ]);

        $this->assertDatabaseHas('prices', [
            'id' => $price->id,
            'price' => $data['price'],
            'date' => $data['date'],
        ]);
    }

    public function test_delete_should_not_return_price()
    {
        $price = Price::factory()->create();

        $response = $this->deleteJson("/api/v1/prices/{$price->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/prices/{$price->id}");
        $response->assertStatus(404)
            ->assertJson([
                'message' => "Service Error: can't find Price"
            ]);
    }
}
