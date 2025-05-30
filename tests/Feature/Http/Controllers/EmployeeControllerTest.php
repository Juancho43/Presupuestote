<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Traits\WithAuthentication;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }
    public function test_index_returns_employees_list(): void
    {
        Employee::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/employees');

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
                        ],
                        'salary',
                        'start_date',
                        'end_date',
                        'is_active',

                    ]
                ]
            ]);
    }

    public function test_show_returns_employee_information()
    {
        $employee = Employee::factory()->create();

        $response = $this->getJson("/api/v1/employees/{$employee->id}");

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
                    ],
                    'salary',
                    'start_date',
                    'end_date',
                    'is_active',
                    'salaries'=>[
                        '*' => [
                            'id',
                            'amount',
                            'date'
                        ]
                    ]
                ]
            ]);
    }

    public function test_store_creates_new_employee()
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
            'salary' => $this->faker->numberBetween(30000, 100000),
            'start_date' => $this->faker->date(),
            'end_date' => null, // Assuming end_date is nullable
            'is_active' => true

        ];

        $response = $this->postJson('/api/v1/employees', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'salary',
                    'start_date',
                    'end_date',
                    'is_active',
                ]
            ]);
    }

    public function test_update_updates_existing_employee()
    {
        $employee = Employee::factory()->create();

        $updateData = [
            'id' => $employee->person->id,
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'mail' => $this->faker->unique()->safeEmail(),
            'dni' => $this->faker->unique()->numerify('########'),
            'cuit' => $this->faker->unique()->numerify('##-########-#'),
            'is_active' => true,
            'salary' => $this->faker->numberBetween(30000, 100000),
            'start_date' => $this->faker->date(),
            'end_date' => null // Assuming end_date is nullable
            ];


        $response = $this->putJson("/api/v1/employees/{$employee->id}", $updateData);

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
                    ],
                    'salary',
                    'start_date',
                    'end_date',
                    'is_active',
                ]
            ]);

        $this->assertDatabaseHas('people', [
            'name' => $updateData['name'],
            'last_name' => $updateData['last_name'],
            'dni' => $updateData['dni'],
            'cuit' => $updateData['cuit']
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'salary' => $updateData['salary']
        ]);
    }

    public function test_delete_should_not_return_employee()
    {
        $employee = Employee::factory()->create();

        $response = $this->deleteJson("/api/v1/employees/{$employee->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/employees/{$employee->id}");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false,
            ]);
    }
}
