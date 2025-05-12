<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Budget;
use App\Enums\WorkStatus;
use App\Http\Requests\WorkRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new WorkRequest();
        return $request->rules();
    }

    public function test_valid_work_passes_validation()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'order' => 1,
            'name' => 'Test Work',
            'notes' => 'Test Notes',
            'estimated_time' => 120,
            'dead_line' => '2024-12-31',
            'cost' => '100.50',
            'status' => WorkStatus::PRESUPUESTADO->value,
            'budget_id' => $budget->id
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_required_fields()
    {
        $validator = Validator::make([], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('order', $validator->errors()->toArray());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('estimated_time', $validator->errors()->toArray());
        $this->assertArrayHasKey('dead_line', $validator->errors()->toArray());
        $this->assertArrayHasKey('cost', $validator->errors()->toArray());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
        $this->assertArrayHasKey('budget_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_cost()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'order' => 1,
            'name' => 'Test Work',
            'estimated_time' => 120,
            'dead_line' => '2024-12-31',
            'cost' => '-100.50',
            'status' => WorkStatus::PRESUPUESTADO->value,
            'budget_id' => $budget->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cost', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_estimated_time()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'order' => 1,
            'name' => 'Test Work',
            'estimated_time' => -1,
            'dead_line' => '2024-12-31',
            'cost' => '100.50',
            'status' => WorkStatus::PRESUPUESTADO->value,
            'budget_id' => $budget->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('estimated_time', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_status()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'order' => 1,
            'name' => 'Test Work',
            'estimated_time' => 120,
            'dead_line' => '2024-12-31',
            'cost' => '100.50',
            'status' => 'invalid-status',
            'budget_id' => $budget->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_budget()
    {
        $validator = Validator::make([
            'order' => 1,
            'name' => 'Test Work',
            'estimated_time' => 120,
            'dead_line' => '2024-12-31',
            'cost' => '100.50',
            'status' => WorkStatus::PRESUPUESTADO->value,
            'budget_id' => 999
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('budget_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_date()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'order' => 1,
            'name' => 'Test Work',
            'estimated_time' => 120,
            'dead_line' => 'invalid-date',
            'cost' => '100.50',
            'status' => WorkStatus::PRESUPUESTADO->value,
            'budget_id' => $budget->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('dead_line', $validator->errors()->toArray());
    }
}
