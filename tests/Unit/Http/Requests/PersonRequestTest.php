<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Person;
use App\Http\Requests\PersonRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(array $data): array
    {
        $request = new PersonRequest();
        return $request->rules();
    }

    public function test_valid_person_data_passes_validation()
    {
        $validator = Validator::make([
            'name' => 'John',
            'phone_number' => '1234567890',
            'last_name' => 'Doe',
            'mail' => 'john@example.com',
            'dni' => '12345678',
            'cuit' => '20-12345678-9',
            'address' => '123 Street'
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_only_name_and_phone_are_required()
    {
        $validator = Validator::make([
            'name' => 'John',
            'phone_number' => '1234567890'
        ], $this->getRequest([]));

        $this->assertFalse($validator->fails());
    }

    public function test_name_is_required()
    {
        $validator = Validator::make([
            'phone_number' => '1234567890'
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_phone_number_is_required()
    {
        $validator = Validator::make([
            'name' => 'John'
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('phone_number', $validator->errors()->toArray());
    }

    public function test_mail_must_be_valid_when_provided()
    {
        $validator = Validator::make([
            'name' => 'John',
            'phone_number' => '1234567890',
            'mail' => 'invalid-email'
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('mail', $validator->errors()->toArray());
    }

    public function test_mail_must_be_unique_when_provided()
    {
        Person::factory()->create(['mail' => 'existing@example.com']);

        $validator = Validator::make([
            'name' => 'John',
            'phone_number' => '1234567890',
            'mail' => 'existing@example.com'
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('mail', $validator->errors()->toArray());
    }

    public function test_dni_must_be_unique_when_provided()
    {
        Person::factory()->create(['dni' => '12345678']);

        $validator = Validator::make([
            'name' => 'John',
            'phone_number' => '1234567890',
            'dni' => '12345678'
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('dni', $validator->errors()->toArray());
    }

    public function test_cuit_must_be_unique_when_provided()
    {
        Person::factory()->create(['cuit' => '20-12345678-9']);

        $validator = Validator::make([
            'name' => 'John',
            'phone_number' => '1234567890',
            'cuit' => '20-12345678-9'
        ], $this->getRequest([]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cuit', $validator->errors()->toArray());
    }
}
