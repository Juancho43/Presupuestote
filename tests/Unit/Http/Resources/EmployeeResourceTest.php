<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployeeResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the EmployeeResource correctly transforms an Employee model.
     *
     */
    #[Test] public function test_employee_resource_has_correct_format(): void
    {
        // Create an employee with known data
        $employee = Employee::factory()->create([
            'salary' => 1000.50,
            'start_date' => '2024-03-20 10:00:00',
            'end_date' => '2024-12-31 10:00:00',
            'is_active' => true,
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new EmployeeResource($employee);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($employee->id, $jsonData['id']);
        $this->assertEquals(1000.50, $jsonData['salary']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['start_date']);
        $this->assertEquals('2024-12-31 10:00:00', $jsonData['end_date']);
        $this->assertTrue($jsonData['is_active']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);

        // Assert relationships exist in the resource
        $this->assertArrayHasKey('person', $jsonData);
        $this->assertArrayHasKey('salaries', $jsonData);
    }
}
