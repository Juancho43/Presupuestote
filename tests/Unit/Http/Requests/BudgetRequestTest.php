<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Client;
use App\Enums\BudgetStatus;
use App\Http\Requests\BudgetRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(array $data): array
    {
        $request = new BudgetRequest();
        return $request->rules();
    }

    public function test_valid_budget_passes_validation()
    {
        $client = Client::factory()->create();

        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'cost' => '1000.00',
            'client_id' => $client->id
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_budget_without_cost_passes_validation()
    {
        $client = Client::factory()->create();

        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'client_id' => $client->id
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_deadline_must_be_after_made_date()
    {
        $client = Client::factory()->create();

        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-03-19',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'cost' => '1000.00',
            'client_id' => $client->id
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('dead_line', $validator->errors()->toArray());
    }



    public function test_non_existent_client_fails_validation()
    {
        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'cost' => '1000.00',
            'client_id' => 999
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('client_id', $validator->errors()->toArray());
    }

    public function test_invalid_status_fails_validation()
    {
        $client = Client::factory()->create();

        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => 'INVALID_STATUS',
            'cost' => '1000.00',
            'client_id' => $client->id
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_negative_cost_fails_validation()
    {
        $client = Client::factory()->create();

        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'cost' => '-100.00',
            'client_id' => $client->id
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cost', $validator->errors()->toArray());
    }

    public function test_valid_budget_with_person_details_passes_validation()
    {
        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'cost' => '1000.00',
            'person' => [
                'name' => 'John Doe',
                'phone_number' => '1234567890'
            ]
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_client_id_or_person_details()
    {
        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'cost' => '1000.00'
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('client_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_incomplete_person_details()
    {
        $validator = Validator::make([
            'made_date' => '2024-03-20',
            'description' => 'Test Budget',
            'dead_line' => '2024-04-20',
            'status' => BudgetStatus::PRESUPUESTADO->value,
            'cost' => '1000.00',
            'person' => [
                'name' => 'John Doe'
            ]
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('person.phone_number', $validator->errors()->toArray());
    }
}
