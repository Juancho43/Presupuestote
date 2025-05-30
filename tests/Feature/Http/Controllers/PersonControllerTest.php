<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\WithAuthentication;
use Tests\TestCase;

class PersonControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }
    public function test_index_returns_person_list(): void
    {
        Person::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/people');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'last_name',
                        'mail',
                        'phone_number',
                        'address',
                        'dni',
                        'cuit'
                    ]
                ]
            ]);
    }

    public function test_show_returns_person_information()
    {
        $person = Person::factory()->create();

        $response = $this->getJson("/api/v1/people/{$person->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'last_name',
                    'mail',
                    'phone_number',
                    'address',
                    'dni',
                    'cuit'
                ]
            ]);
    }

    public function test_store_creates_new_person()
    {
        $data = [
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'mail' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'dni' => $this->faker->unique()->numerify('########'),
            'cuit' => $this->faker->unique()->numerify('##-########-#'),
        ];

        $response = $this->postJson('/api/v1/people', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'last_name',
                    'mail',
                    'phone_number',
                    'address',
                    'dni',
                    'cuit'
                ]
            ]);

        $this->assertDatabaseHas('people', [
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'mail' => $data['mail'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'dni' => $data['dni'],
            'cuit' => $data['cuit'],
        ]);

    }

    public function test_update_updates_existing_person()
    {
        $person = Person::factory()->create();
        $data = [
            'id' => $person->id,
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'mail' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'dni' => $this->faker->unique()->numerify('########'),
            'cuit' => $this->faker->unique()->numerify('##-########-#'),
        ];


        $response = $this->putJson("/api/v1/people/{$person->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'last_name',
                    'mail',
                    'phone_number',
                    'address',
                    'dni',
                    'cuit'
                ]
            ]);

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'mail' => $data['mail'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'dni' => $data['dni'],
            'cuit' => $data['cuit'],
        ]);
    }

    public function test_delete_should_not_return_person()
    {
        $person = Person::factory()->create();

        $response = $this->deleteJson("/api/v1/people/{$person->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/people/{$person->id}");
        $response->assertStatus(404)
            ->assertJson([
                'message' => "Service Error: can't find Person"
            ]);
    }
}
