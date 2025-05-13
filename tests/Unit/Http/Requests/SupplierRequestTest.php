<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\V1\SupplierRequest;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SupplierRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(array $data): array
    {
        $request = new SupplierRequest();
        return $request->rules();
    }

    public function test_valid_supplier_with_person_id_passes_validation()
    {
        $person = Person::factory()->create();

        $validator = Validator::make([
            'person_id' => $person->id,
            'balance' => '0.00',
            'notes' => 'Some notes'
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_valid_supplier_with_new_person_passes_validation()
    {
        $validator = Validator::make([
            'balance' => '0.00',
            'person' => [
                'name' => 'John Doe',
                'phone_number' => '1234567890'
            ]
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_balance_is_not_required()
    {
        $validator = Validator::make([
            'person' => [
                'name' => 'John Doe',
                'phone_number' => '1234567890'
            ]
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_balance_must_be_numeric()
    {
        $validator = Validator::make([
            'balance' => 'invalid',
            'person' => [
                'name' => 'John Doe',
                'phone_number' => '1234567890'
            ]
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('balance', $validator->errors()->toArray());
    }
}
