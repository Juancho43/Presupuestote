<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the PersonResource correctly transforms a Person model.
     */
    #[Test]
    public function test_person_resource_has_correct_format(): void
    {
        // Create a person with known data
        $person = Person::factory()->create([
            'name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main St',
            'phone_number' => '1234567890',
            'mail' => 'john@example.com',
            'dni' => '12345678',
            'cuit' => '20123456789',
            'created_at' => '2024-03-20 10:00:00',
            'updated_at' => '2024-03-20 10:00:00',
        ]);

        // Transform the model using the resource
        $resource = new PersonResource($person);
        $jsonData = $resource->toArray(request());

        // Assert the transformed data matches expected format
        $this->assertEquals($person->id, $jsonData['id']);
        $this->assertEquals('John', $jsonData['name']);
        $this->assertEquals('Doe', $jsonData['last_name']);
        $this->assertEquals('123 Main St', $jsonData['address']);
        $this->assertEquals('1234567890', $jsonData['phone_number']);
        $this->assertEquals('john@example.com', $jsonData['mail']);
        $this->assertEquals('12345678', $jsonData['dni']);
        $this->assertEquals('20123456789', $jsonData['cuit']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['created_at']);
        $this->assertEquals('2024-03-20 10:00:00', $jsonData['updated_at']);
        $this->assertNull($jsonData['deleted_at']);
    }

    /**
     * Test that the PersonResource handles null values correctly.
     */
    #[Test]
    public function test_person_resource_handles_null_values(): void
    {
        $person = Person::factory()->create([
            'address' => null,
            'cuit' => null,
        ]);

        $resource = new PersonResource($person);
        $jsonData = $resource->toArray(request());

        $this->assertNull($jsonData['address']);
        $this->assertNull($jsonData['cuit']);
    }
}
