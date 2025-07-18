<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\V1\PriceRequest;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PriceRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new PriceRequest();
        return $request->rules();
    }

    public function test_valid_price_passes_validation()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'price' => '100.50',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_required_fields()
    {
        $validator = Validator::make([], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('material_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_material_id()
    {
        $validator = Validator::make([
            'material_id' => 999,
            'price' => '100.50',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('material_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_negative_price()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'price' => '-100.50',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_price_decimals()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'price' => '100.555',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_date()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'price' => '100.50',
            'date' => 'invalid-date'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }
}
