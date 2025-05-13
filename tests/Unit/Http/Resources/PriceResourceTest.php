<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PriceResource;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PriceResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the PriceResource correctly transforms a Price model.
     */
    #[Test]
    public function test_price_resource_has_correct_format(): void
    {
        // Create a price with known data
        $price = Price::factory()->create([
            'price' => 150.50,
            'date' => '2024-03-20',
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new PriceResource($price);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($price->id, $jsonData['id']);
        $this->assertEquals(150.50, $jsonData['price']);
        $this->assertEquals('2024-03-20', $jsonData['date']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);

        // Assert relationship exists in the resource
        $this->assertArrayHasKey('material', $jsonData);
    }
}
