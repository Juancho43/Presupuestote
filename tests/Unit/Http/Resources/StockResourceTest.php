<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\V1\StockResource;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_stock_resource_has_correct_format(): void
    {
        $stock = Stock::factory()->create([
            'stock' => 100,
            'date' => '2024-03-20',
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        $resource = new StockResource($stock);
        $jsonData = $resource->toArray(request());

        $this->assertEquals($stock->id, $jsonData['id']);
        $this->assertEquals(100, $jsonData['stock']);
        $this->assertEquals('2024-03-20', $jsonData['date']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);
    }
}
