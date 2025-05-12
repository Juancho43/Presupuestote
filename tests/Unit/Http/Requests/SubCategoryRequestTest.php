<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Category;
use App\Http\Requests\SubCategoryRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubCategoryRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new SubCategoryRequest();
        return $request->rules();
    }

    public function test_valid_subcategory_passes_validation()
    {
        $category = Category::factory()->create();

        $validator = Validator::make([
            'name' => 'Test Subcategory',
            'description' => 'Test Description',
            'category_id' => $category->id
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_required_fields()
    {
        $validator = Validator::make([], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_long_name()
    {
        $category = Category::factory()->create();

        $validator = Validator::make([
            'name' => str_repeat('a', 256),
            'category_id' => $category->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_nonexistent_category()
    {
        $validator = Validator::make([
            'name' => 'Test Subcategory',
            'category_id' => 999
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
    }

    public function test_description_is_optional()
    {
        $category = Category::factory()->create();

        $validator = Validator::make([
            'name' => 'Test Subcategory',
            'category_id' => $category->id
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }


}
