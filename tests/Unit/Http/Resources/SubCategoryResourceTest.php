<?php

namespace Tests\Unit\Http\Resources;

use App\Models\SubCategory;
use Tests\TestCase;
use App\Models\Category;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubCategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_subcategory_resource_transformation()
    {
        // Create a category and subcategory
        $category = Category::factory()->create();
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        // Create the resource
        $subcategoryResource = new SubCategoryResource($subcategory);

        // Get the transformed array
        $subcategoryArray = $subcategoryResource->toArray(request());

        // Assert the structure and data
        $this->assertArrayHasKey('id', $subcategoryArray);
        $this->assertArrayHasKey('name', $subcategoryArray);
        $this->assertArrayHasKey('category', $subcategoryArray);
        $this->assertArrayHasKey('created_at', $subcategoryArray);
        $this->assertArrayHasKey('updated_at', $subcategoryArray);
        $this->assertArrayHasKey('deleted_at', $subcategoryArray);

        $this->assertEquals($subcategory->id, $subcategoryArray['id']);
        $this->assertEquals($subcategory->name, $subcategoryArray['name']);
        $this->assertEquals($subcategory->created_at->toDateTimeString(), $subcategoryArray['created_at']);
        $this->assertEquals($subcategory->updated_at->toDateTimeString(), $subcategoryArray['updated_at']);
        $this->assertEquals($subcategory->deleted_at?->toDateTimeString(), $subcategoryArray['deleted_at']);
    }


}
