<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the InvoiceResource correctly transforms an Invoice model.
     *
     */
    #[Test] public function test_invoice_resource_has_correct_format(): void
    {
        // Create an invoice with known data
        $invoice = Invoice::factory()->create([
            'date' => '2024-03-20',
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new InvoiceResource($invoice);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($invoice->id, $jsonData['id']);
        $this->assertEquals('2024-03-20', $jsonData['date']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);

        // Assert relationships exist in the resource
        $this->assertArrayHasKey('materials', $jsonData);
        $this->assertArrayHasKey('payments', $jsonData);
    }
}
