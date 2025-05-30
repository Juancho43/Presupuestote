<?php

namespace Tests\Feature\Services\BudgetServiceTest;

use App\Http\Requests\V1\AddWorksToBudgeRequest;
use App\Models\Budget;
use App\Models\Material;
use App\Models\Price;
use App\Models\Work;
use App\Repository\V1\BudgetRepository;
use App\Services\V1\BudgetService;
use App\Services\V1\WorkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    private BudgetService $budgetService;
    private BudgetRepository $budgetRepository;
    private WorkService $workService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->budgetRepository = $this->mock(BudgetRepository::class);
        $this->workService = $this->mock(WorkService::class);
        $this->budgetService = new BudgetService($this->budgetRepository, $this->workService);
    }

    #[Test] public function it_can_add_works_with_materials_to_budget(): void
    {
        // Arrange
        $budget = Budget::factory()->create(['cost' => 0]);

        $works = Work::factory()
            ->count(2)
            ->create()
            ->each(function ($work) {
                Material::factory()
                    ->count(2)
                    ->has(
                        Price::factory()->state([
                            'price' => 100,
                            'date' => now()
                        ])
                    )
                    ->create()
                    ->each(function ($material) use ($work) {
                        $work->materials()->attach($material->id, [
                            'quantity' => rand(1, 5),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    });
            });
        $request = new AddWorksToBudgeRequest();
        $request->merge([
            'budget_id' => $budget->id,
            'work_ids' => $works->pluck('id')->toArray()
        ]);

        // Mock repository responses
        $this->budgetRepository->shouldReceive('find')
            ->with($budget->id)
            ->once()
            ->andReturn($budget);

        $this->budgetRepository->shouldReceive('addWorks')
            ->with($budget->id, $works->pluck('id')->toArray())
            ->once()
            ->andReturn($budget);

        // Mock work cost calculations
        $this->workService->shouldReceive('calculateWorkCost')
            ->times(2)
            ->andReturn(200.00);

        // Act
        $result = $this->budgetService->addWorksToBudget($request);

        // Assert
        $this->assertInstanceOf(Budget::class, $result);
        $this->assertEquals(400.00, $result->cost);
        $this->assertTrue($result->relationLoaded('works'));
        $this->assertTrue($result->works->first()->relationLoaded('materials'));
    }

    #[Test] public function it_returns_error_response_when_exception_occurs(): void
    {
        // Arrange
        $request = new AddWorksToBudgeRequest();
        $request->merge([
            'budget_id' => 1,
            'work_ids' => [1]
        ]);

        $this->budgetRepository->shouldReceive('find')
            ->andThrow(new \Exception('Database error'));

        // Act
        $result = $this->budgetService->addWorksToBudget($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertEquals('Error adding works to budget', json_decode($result->getContent())->message);
    }
}
