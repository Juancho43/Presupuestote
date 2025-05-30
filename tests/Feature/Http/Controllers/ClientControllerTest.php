<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_clients_list(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/clients');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'person' => [
                            'id',
                            'name',
                            'last_name',
                            'address',
                            'phone_number',
                            'mail',
                            'dni',
                            'cuit'
                        ]
                    ]
                ]
            ]);
    }

    public function test_show_returns_client_information()
    {
        $client = Client::factory()->create();

        $response = $this->getJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'person' => [
                        'id',
                        'name',
                        'last_name',
                        'address',
                        'phone_number',
                        'mail',
                        'dni',
                        'cuit'
                    ]
                ]
            ]);
    }
    public function test_store_creates_new_client()
    {
        $personData = [
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'mail' => $this->faker->unique()->safeEmail(),
            'dni' => $this->faker->unique()->numerify('########'),
            'cuit' => $this->faker->unique()->numerify('##-########-#')
        ];

        $data = [
            'person' => $personData,
            'balance' => 0
        ];

        $response = $this->postJson('/api/v1/clients', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'balance'
                ]
            ]);
    }
    public function test_update_updates_existing_client()
    {
        $client = Client::factory()->create();

        $updateData = [
            'id' => $client->person->id,
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'mail' => $this->faker->unique()->safeEmail(),
            'dni' => $this->faker->unique()->numerify('########'),
            'cuit' => $this->faker->unique()->numerify('##-########-#')
        ];

        $response = $this->putJson("/api/v1/clients/{$client->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'person' => [
                        'id',
                        'name',
                        'last_name',
                        'address',
                        'phone_number',
                        'mail',
                        'dni',
                        'cuit'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('people', $updateData);
    }

    public function test_delete_should_not_return_client()
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson("/api/v1/clients/{$client->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/clients/{$client->id}");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => "Service Error: can't find client"
            ]);
    }
}
