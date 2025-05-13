<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\MaterialResource;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MaterialResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the MaterialResource correctly transforms a Material model.
     *
     */
    #[Test] public function test_material_resource_has_correct_format(): void
    {
        // Create a material with known data
        $material = Material::factory()->create([
            'name' => 'Test Material',
            'description' => 'Test Description',
            'color' => 'Red',
            'brand' => 'Test Brand',
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new MaterialResource($material);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($material->id, $jsonData['id']);
        $this->assertEquals('Test Material', $jsonData['name']);
        $this->assertEquals('Test Description', $jsonData['description']);
        $this->assertEquals('Red', $jsonData['color']);
        $this->assertEquals('Test Brand', $jsonData['brand']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);

        // Assert relationships exist in the resource
        $this->assertArrayHasKey('subcategory', $jsonData);
        $this->assertArrayHasKey('prices', $jsonData);
        $this->assertArrayHasKey('stocks', $jsonData);
        $this->assertArrayHasKey('measure', $jsonData);
    }

    /**
     * Test that the MaterialResource handles null values correctly.
     *
     */
    #[Test] public function test_material_resource_handles_null_values(): void
    {
        $material = Material::factory()->create([
            'description' => null,
            'color' => null,
            'brand' => null,
        ]);

        $resource = new MaterialResource($material);
        $jsonData = $resource->toArray(request());

        $this->assertNull($jsonData['description']);
        $this->assertNull($jsonData['color']);
        $this->assertNull($jsonData['brand']);
    }
}
