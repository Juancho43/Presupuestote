<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\V1\PaymentResource;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the PaymentResource correctly transforms a Payment model.
     *
     */
    #[Test] public function test_payment_resource_has_correct_format(): void
    {
        // Create a payment with known data
        $payment = Payment::factory()->create([
            'amount' => 1500.75,
            'date' => '2024-03-20 10:00:00',
            'description' => 'Test Payment',
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new PaymentResource($payment);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($payment->id, $jsonData['id']);
        $this->assertEquals(1500.75, $jsonData['amount']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['date']);
        $this->assertEquals('Test Payment', $jsonData['description']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);
    }

}
