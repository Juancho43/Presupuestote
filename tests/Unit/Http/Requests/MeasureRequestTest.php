<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\V1\MeasureRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MeasureRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new MeasureRequest();
        return $request->rules();
    }

    public function test_valid_measure_passes_validation()
    {
        $validator = Validator::make([
            'name' => 'Kilogram',
            'abbreviation' => 'kg',
            'unit' => '1.00'
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_required_fields()
    {
        $validator = Validator::make([], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('abbreviation', $validator->errors()->toArray());
        $this->assertArrayHasKey('unit', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_name_length()
    {
        $validator = Validator::make([
            'name' => str_repeat('a', 256),
            'abbreviation' => 'kg',
            'unit' => '1.00'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_abbreviation_length()
    {
        $validator = Validator::make([
            'name' => 'Kilogram',
            'abbreviation' => str_repeat('a', 11),
            'unit' => '1.00'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('abbreviation', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_negative_unit()
    {
        $validator = Validator::make([
            'name' => 'Kilogram',
            'abbreviation' => 'kg',
            'unit' => '-1.00'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('unit', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_unit_decimals()
    {
        $validator = Validator::make([
            'name' => 'Kilogram',
            'abbreviation' => 'kg',
            'unit' => '1.234'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('unit', $validator->errors()->toArray());
    }
}
