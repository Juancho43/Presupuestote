<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\V1\MeasureResource;
use App\Models\Measure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MeasureResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the MeasureResource correctly transforms a Measure model.
     *
     */
    #[Test] public function test_measure_resource_has_correct_format(): void
    {
        // Create a measure with known data
        $measure = Measure::factory()->create([
            'name' => 'Meter',
            'abbreviation' => 'm',
            'unit' => '7.67',
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new MeasureResource($measure);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($measure->id, $jsonData['id']);
        $this->assertEquals('Meter', $jsonData['name']);
        $this->assertEquals('m', $jsonData['abbreviation']);
        $this->assertEquals('7.67', $jsonData['unit']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);
    }



}
