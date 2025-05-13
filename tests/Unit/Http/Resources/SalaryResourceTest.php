<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\V1\SalaryResource;
use App\Models\Salary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SalaryResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the SalaryResource correctly transforms a Salary model.
     */
    #[Test]
    public function test_salary_resource_has_correct_format(): void
    {
        // Create a salary with known data
        $salary = Salary::factory()->create([
            'amount' => 50000.00,
            'date' => '2024-03-20',
            'active' => true,
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new SalaryResource($salary);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($salary->id, $jsonData['id']);
        $this->assertEquals(50000.00, $jsonData['amount']);
        $this->assertEquals('2024-03-20', $jsonData['date']);
        $this->assertTrue($jsonData['active']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);

        // Assert relationship exists in the resource
        $this->assertArrayHasKey('payments', $jsonData);
    }
}
