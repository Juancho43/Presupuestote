<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Material;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StockControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_stock_list(): void
    {
        Stock::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/stocks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'stock',
                        'date',
                    ]
                ]
            ]);
    }

    public function test_show_returns_stock_information()
    {
        $stock = Stock::factory()->create();

        $response = $this->getJson("/api/v1/stocks/{$stock->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'stock',
                    'date',
                    'material'
                ]
            ]);
    }

    public function test_store_creates_new_stock()
    {
        $material = Material::factory()->create();
        $data = [
            'stock' => $this->faker->randomFloat(2, 1, 100),
            'date' => $this->faker->date(),
            'material_id' => $material->id,
        ];

        $response = $this->postJson('/api/v1/stocks', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'stock',
                    'date',
                ]
            ]);

        $this->assertDatabaseHas('stocks', [
            'stock' => $data['stock'],
            'date' => $data['date'],
        ]);

    }

    public function test_update_updates_existing_price()
    {
        $material = Material::factory()->create();
        $stock = Stock::factory()->create();
        $data = [
            'stock' => $this->faker->randomFloat(2, 1, 100),
            'date' => $this->faker->date(),
            'material_id' => $material->id,
        ];


        $response = $this->putJson("/api/v1/stocks/{$stock->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'stock',
                    'date',
                ]
            ]);

        $this->assertDatabaseHas('stocks', [
            'id' => $stock->id,
            'stock' => $data['stock'],
            'date' => $data['date'],
        ]);
    }

    public function test_delete_should_not_return_stock()
    {
        $stock = Stock::factory()->create();

        $response = $this->deleteJson("/api/v1/stocks/{$stock->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/stocks/{$stock->id}");
        $response->assertStatus(404)
            ->assertJson([
                'message' => "Service Error: can't find Stock"
            ]);
    }
}
