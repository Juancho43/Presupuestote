<?php

     namespace Tests\Unit\Http\Resources;

     use App\Enums\BudgetStatus;
     use App\Http\Resources\BudgetResource;
     use App\Models\Budget;
     use Illuminate\Foundation\Testing\RefreshDatabase;
     use Tests\TestCase;

     class BudgetResourceTest extends TestCase
     {
         use RefreshDatabase;

         /**
          * Test that the BudgetResource correctly transforms a Budget model.
          *
          */
         public function test_budget_resource_has_correct_format(): void
         {
             // Create a budget with known data
             $budget = Budget::factory()->create([
                 'made_date' => '2024-03-20',
                 'description' => 'Test Budget',
                 'dead_line' => '2024-04-20',
                 'status' => BudgetStatus::PRESUPUESTADO->value,
                 'cost' => 1000.50,
                 'created_at' => '2024-03-20 10:00:00',
                 'updated_at' => '2024-03-20 10:00:00',
             ]);

             // Transform the model using the resource
             $resource = new BudgetResource($budget);
             $jsonData = $resource->toArray(request());

             // Assert the transformed data matches expected format
             $this->assertEquals($budget->id, $jsonData['id']);
             $this->assertEquals('2024-03-20', $jsonData['made_date']);
             $this->assertEquals('Test Budget', $jsonData['description']);
             $this->assertEquals('2024-04-20', $jsonData['dead_line']);
             $this->assertEquals(BudgetStatus::PRESUPUESTADO->value, $jsonData['status']);
             $this->assertEquals(1000.50, $jsonData['cost']);
             $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
             $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
             $this->assertNull($jsonData['deleted_at']);
             $this->assertArrayHasKey('payments', $jsonData);
             $this->assertArrayHasKey('works', $jsonData);
         }


     }
