<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\V1\WorkResource;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_work_resource_has_correct_format(): void
    {
        $work = Work::factory()->create([
            'order' => 1,
            'name' => 'Test Work',
            'notes' => 'Test notes',
            'estimated_time' => '2:00:00',
            'dead_line' => '2024-03-20',
            'cost' => 500.75,
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        $resource = new WorkResource($work);
        $jsonData = $resource->toArray(request());

        $this->assertEquals($work->id, $jsonData['id']);
        $this->assertEquals(1, $jsonData['order']);
        $this->assertEquals('Test Work', $jsonData['name']);
        $this->assertEquals('Test notes', $jsonData['notes']);
        $this->assertEquals('2', $jsonData['estimated_time']);
        $this->assertEquals('2024-03-20', $jsonData['dead_line']);
        $this->assertEquals(500.75, $jsonData['cost']);
        $this->assertNotNull($jsonData['status']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);
        $this->assertArrayHasKey('materials', $jsonData);
    }

}
