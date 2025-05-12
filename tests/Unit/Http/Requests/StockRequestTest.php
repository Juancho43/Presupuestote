<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Material;
use App\Http\Requests\StockRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new StockRequest();
        return $request->rules();
    }

    public function test_valid_stock_passes_validation()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'stock' => '100.50',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_required_fields()
    {
        $validator = Validator::make([], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('material_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_material_id()
    {
        $validator = Validator::make([
            'material_id' => 999,
            'stock' => '100.50',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('material_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_negative_stock()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'stock' => '-100.50',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_stock_decimals()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'stock' => '100.555',
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_date()
    {
        $material = Material::factory()->create();

        $validator = Validator::make([
            'material_id' => $material->id,
            'stock' => '100.50',
            'date' => 'invalid-date'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }
}
