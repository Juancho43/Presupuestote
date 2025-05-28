<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BudgetControllerTest extends TestCase
{
    use RefreshDatabase,  WithFaker;
    public function test_index_returns_budgets_list(): void
    {
        Budget::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/budgets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'description',
                        'made_date',
                        "dead_line",
                        'state',
                        'payment_status',
                        'cost',
                        'profit',
                        'price',
                        'client'
                    ]
                ]
            ]);
    }

    public function test_show_returns_budget_information()
    {
        $budget = Budget::factory()->create();

        $response = $this->getJson("/api/v1/budgets/{$budget->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' =>  [
                    'id',
                    'description',
                    'made_date',
                    "dead_line",
                    'state',
                    'payment_status',
                    'cost',
                    'profit',
                    'price',
                    'payments',
                    'works'
               ]
            ]);

    }

    public function test_store_creates_new_budget()
    {
        $data = Budget::factory()->create([
            'made_date' => now(),
            'dead_line' => now()->addDays(30),
        ])->toArray();

        $response = $this->postJson('/api/v1/budgets', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'description','made_date',"dead_line", 'state','payment_status','cost','profit','price']
            ]);
    }

    public function test_update_updates_existing_budget()
    {

        $budget = Budget::factory()->create();
        $data = Budget::factory()->make()->toArray();

        $response = $this->putJson("/api/v1/budgets/{$budget->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'description','made_date',"dead_line", 'state','payment_status','cost','profit','price']
            ]);
    }

    public function test_delete_should_not_return_budget()
    {
        $budget = Budget::factory()->count(2)->create();
        $response = $this->deleteJson("/api/v1/budgets/{$budget[0]->id}");
        $response->assertStatus(204);
        $response = $this->getJson("/api/v1/budgets/{$budget[0]->id}");
        $response->assertStatus(500)
            ->assertJson([
                'message' => "Service Error: can't find Budget"
            ]);

    }

    public function test_call_method_update_price(){
        $budget = Budget::factory()->create([
            'cost' => 0,
            'price' => 200,
            'profit' => 200,
        ]);

        $budget->works()->create([
            'budget_id' => $budget->id,
            'cost' => 800,
            'order' => 1,
            'name' => 'Test Work',
            'notes' => 'Test notes',
            'estimated_time' => 10,
            'dead_line' => now()->addDays(30),
            'state' => 'Presupuestado',

        ]);
        $budget->save();

        $response = $this->get('/api/v1/budgets/updatePrice/'.$budget->id);
        $response->assertStatus(200)
            ->assertJson([
                'message' => "Budget price updated successfully",
                'data' => [
                    'id' => $budget->id,
                    'price' => 1000, // Assuming the price is updated to 1000
                ]
            ]);

    }

}
