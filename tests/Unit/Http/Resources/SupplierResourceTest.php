<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_supplier_resource_has_correct_format(): void
    {
        $supplier = Supplier::factory()->create([
            'notes' => 'Test notes',
            'balance' => 1000.50,
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        $resource = new SupplierResource($supplier);
        $jsonData = $resource->toArray(request());

        $this->assertEquals($supplier->id, $jsonData['id']);
        $this->assertEquals('Test notes', $jsonData['notes']);
        $this->assertEquals(1000.50, $jsonData['balance']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);
        $this->assertArrayHasKey('person', $jsonData);
        $this->assertArrayHasKey('invoices', $jsonData);
    }

}
