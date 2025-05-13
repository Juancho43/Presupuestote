<?php

namespace Tests\Unit\Http\Resources;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    use RefreshDatabase;
    public function test_category_resource_transformation()
    {
        // Create a category
        $category = Category::factory()->create();

        // Create the resource
        $categoryResource = new CategoryResource($category);

        // Get the transformed array
        $categoryArray = $categoryResource->toArray(request());

        // Assert the structure and data
        $this->assertArrayHasKey('id', $categoryArray);
        $this->assertArrayHasKey('name', $categoryArray);
        $this->assertArrayHasKey('created_at', $categoryArray);
        $this->assertArrayHasKey('updated_at', $categoryArray);
        $this->assertArrayHasKey('deleted_at', $categoryArray);

        $this->assertEquals($category->id, $categoryArray['id']);
        $this->assertEquals($category->name, $categoryArray['name']);
        $this->assertEquals($category->created_at->toDateTimeString(), $categoryArray['created_at']);
        $this->assertEquals($category->updated_at->toDateTimeString(), $categoryArray['updated_at']);
        $this->assertEquals($category->deleted_at?->toDateTimeString(), $categoryArray['deleted_at']);
    }
}
