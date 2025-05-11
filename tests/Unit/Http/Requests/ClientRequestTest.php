<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Person;
use App\Http\Requests\ClientRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(array $data): array
    {
        $request = new ClientRequest();
        return $request->rules();
    }

    public function test_valid_nested_person_request()
    {
        $validator = Validator::make([
            'balance' => '100.00',
            'person' => [
                'name' => 'John',
                'phone_number' => '1234567890',
                'last_name' => 'Doe',
                'mail' => 'john@example.com',
                'address' => '123 Street'
            ]
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_validates_required_nested_fields()
    {
        $validator = Validator::make([
            'balance' => '100.00',
            'person' => [
                'last_name' => 'Doe'
            ]
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('person.name', $validator->errors()->toArray());
        $this->assertArrayHasKey('person.phone_number', $validator->errors()->toArray());
    }

    public function test_validates_existing_person_id()
    {
        $person = Person::factory()->create();

        $validator = Validator::make([
            'balance' => '100.00',
            'person_id' => $person->id
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_balance_is_not_required()
    {
        $validator = Validator::make([
            'person' => [
                'name' => 'John',
                'phone_number' => '1234567890'
            ]
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }
}
