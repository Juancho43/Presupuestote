<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Traits\WithAuthentication;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }
    public function test_index_returns_suppliers_list(): void
    {
        Supplier::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/suppliers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'notes',
                        'balance',
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
    public function test_show_returns_supplier_information()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->getJson("/api/v1/suppliers/{$supplier->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'notes',
                    'balance',
                    'invoices',
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
    public function test_store_creates_new_supplier()
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
            'balance' => 0,
            'notes' => $this->faker->sentence(),
        ];

        $response = $this->postJson('/api/v1/suppliers', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'balance',
                    'notes',
                ]
            ]);
    }
    public function test_update_updates_existing_supplier()
    {
        $supplier = Supplier::factory()->create();

        $updateData = [
            'person_id' => $supplier->person->id,
            'notes' => $this->faker->sentence(),
        ];

        $response = $this->putJson("/api/v1/suppliers/{$supplier->id}", $updateData);

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
    public function test_delete_should_not_return_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson("/api/v1/suppliers/{$supplier->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/suppliers/{$supplier->id}");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => "Service Error: can't find Supplier"
            ]);
    }
}
