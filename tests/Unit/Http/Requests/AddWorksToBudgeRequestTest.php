<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Work;
use App\Models\Budget;
use App\Enums\WorkStatus;
use App\Http\Requests\AddWorksToBudgeRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddWorksToBudgeRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new AddWorksToBudgeRequest();
        return $request->rules();
    }

    public function test_valid_request_with_work_ids_passes_validation()
    {
        $budget = Budget::factory()->create();
        $works = Work::factory(2)->create();

        $validator = Validator::make([
            'budget_id' => $budget->id,
            'work_ids' => $works->pluck('id')->toArray()
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_valid_request_with_new_works_passes_validation()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'budget_id' => $budget->id,
            'works' => [
                [
                    'name' => 'Test Work',
                    'notes' => 'Test Notes',
                    'estimated_time' => 60,
                    'dead_line' => '2024-04-20',
                    'cost' => '100.00',
                    'status' => WorkStatus::PRESUPUESTADO->value
                ]
            ]
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_budget_id()
    {
        $works = Work::factory(2)->create();

        $validator = Validator::make([
            'work_ids' => $works->pluck('id')->toArray()
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('budget_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_non_existent_work_ids()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'budget_id' => $budget->id,
            'work_ids' => [999, 888]
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('work_ids.0', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_work_data()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'budget_id' => $budget->id,
            'works' => [
                [
                    'notes' => 'Test Notes',
                    'cost' => 'invalid'
                ]
            ]
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('works.0.name', $validator->errors()->toArray());
        $this->assertArrayHasKey('works.0.dead_line', $validator->errors()->toArray());
        $this->assertArrayHasKey('works.0.cost', $validator->errors()->toArray());
    }

    public function test_fails_validation_without_works_or_work_ids()
    {
        $budget = Budget::factory()->create();

        $validator = Validator::make([
            'budget_id' => $budget->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('work_ids', $validator->errors()->toArray());
    }
}
