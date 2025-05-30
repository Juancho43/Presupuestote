<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Invoice;
use App\Models\Material;
use App\Models\Price;
use App\Models\Stock;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Traits\WithAuthentication;
use Tests\TestCase;

class WorkControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }
    public function test_index_returns_works_list(): void
    {
        Work::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/works');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'order',
                        'name',
                        'notes',
                        'estimated_time',
                        'dead_line',
                        'cost',
                        'state',
                        'budget',
                    ]
                ]
            ]);
    }

    public function test_show_returns_work_information()
    {
        $Work = Work::factory()->create();

        $response = $this->getJson("/api/v1/works/{$Work->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' =>  [
                    'id',
                    'order',
                    'name',
                    'notes',
                    'estimated_time',
                    'dead_line',
                    'cost',
                    'state',
                    'budget',
                    'materials',
                ]
            ]);

    }

    public function test_store_creates_new_work()
    {
        $budget = \App\Models\Budget::factory()->create();

        $data = [
            'order' => $this->faker->numberBetween(1, 100),
            'name' => $this->faker->words(3, true),
            'notes' => $this->faker->sentence(),
            'estimated_time' => $this->faker->numberBetween(1, 100),
            'dead_line' => now()->addDays(30)->format('Y-m-d H:i:s'), // Add the missing dead_line
            'budget_id' => $budget->id
        ];

        $response = $this->postJson('/api/v1/works', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order',
                    'name',
                    'notes',
                    'estimated_time',
                    'dead_line',
                    'cost',
                    'state',

                ]
            ]);
    }

    public function test_update_updates_existing_work()
    {
        $work = Work::factory()->create();

        $data = [
            'order' => $this->faker->numberBetween(1, 100),
            'name' => $this->faker->words(3, true),
            'notes' => $this->faker->sentence(),
            'estimated_time' => $this->faker->numberBetween(1, 100),
            'dead_line' => now()->addDays(30)->format('Y-m-d H:i:s'), // Add the missing dead_line
            'budget_id' => $work->budget_id
        ];
        $response = $this->putJson("/api/v1/works/{$work->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order',
                    'name',
                    'notes',
                    'estimated_time',
                    'dead_line',
                    'cost',
                    'state',

                ]
            ]);
    }

    public function test_delete_should_not_return_work()
    {
        $works = Work::factory()->create();
        $response = $this->deleteJson("/api/v1/works/{$works->id}");
        $response->assertStatus(204);
        $response = $this->getJson("/api/v1/works/{$works->id}");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => "Service Error: can't find Work"
            ]);

    }
     public function test_can_change_state()
     {
         $work = Work::factory()->create();
         $response = $this->postJson('/api/v1/works/states/'.$work->id.'/Cancelado');

            $response->assertStatus(200)
                ->assertJson([
                    'message' => "State changed successfully",
                    'data' => [
                        'id' => $work->id,
                        'state' => 'Cancelado'
                    ]
                ]);
     }

    public function test_add_materials_to_invoice()
    {
        $work = Work::factory()->create();

        $material = Material::factory()->create();
        Stock::factory()->create([
            'material_id' => $material->id,
            'stock' => 100
        ]);
        Price::factory()->create([
            'material_id' => $material->id,
            'price' => 50.00
        ]);
        $data = [
            'work_id' => $work->id,
            'materials' => [
                [
                    'id' => $material->id,
                    'quantity' => 2,
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/works/materials', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'materials' => [
                        '*' => [
                            'id',
                            'quantity',
                            'latestPrice'
                        ]
                    ]
                ]
            ]);

    }
}
