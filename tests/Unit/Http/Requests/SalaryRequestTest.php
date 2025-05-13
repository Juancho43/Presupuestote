<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\V1\SalaryRequest;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SalaryRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new SalaryRequest();
        return $request->rules();
    }

    public function test_valid_salary_with_employee_id_passes_validation()
    {
        $employee = Employee::factory()->create();

        $validator = Validator::make([
            'amount' => '1000.00',
            'date' => '2024-03-20',
            'active' => true,
            'employee_id' => $employee->id
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_valid_salary_with_person_details_passes_validation()
    {
        $validator = Validator::make([
            'amount' => '1000.00',
            'date' => '2024-03-20',
            'active' => true,
            'person' => [
                'name' => 'John Doe',
                'phone_number' => '1234567890'
            ]
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_employee_or_person()
    {
        $validator = Validator::make([
            'amount' => '1000.00',
            'date' => '2024-03-20',
            'active' => true
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('employee_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_incomplete_person_details()
    {
        $validator = Validator::make([
            'amount' => '1000.00',
            'date' => '2024-03-20',
            'active' => true,
            'person' => [
                'name' => 'John Doe'
            ]
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('person.phone_number', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_amount()
    {
        $employee = Employee::factory()->create();

        $validator = Validator::make([
            'amount' => '-1000.00',
            'date' => '2024-03-20',
            'active' => true,
            'employee_id' => $employee->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('amount', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_date()
    {
        $employee = Employee::factory()->create();

        $validator = Validator::make([
            'amount' => '1000.00',
            'date' => 'invalid-date',
            'active' => true,
            'employee_id' => $employee->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_non_existent_employee()
    {
        $validator = Validator::make([
            'amount' => '1000.00',
            'date' => '2024-03-20',
            'active' => true,
            'employee_id' => 999
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('employee_id', $validator->errors()->toArray());
    }
}
