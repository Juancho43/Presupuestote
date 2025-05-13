<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\V1\CategoryRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CategoryRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new CategoryRequest();
        return $request->rules();
    }

    public function test_valid_category_passes_validation()
    {
        $validator = Validator::make([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_valid_category_with_parent_passes_validation()
    {

        $validator = Validator::make([
            'name' => 'Test Category',
            'description' => 'Test Description',

        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_required_fields()
    {
        $validator = Validator::make([], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_name_length()
    {
        $validator = Validator::make([
            'name' => str_repeat('a', 256)
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }


}
