<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\WithAuthentication;
use Tests\TestCase;

class SalaryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }
    public function test_index_returns_salary_list(): void
    {
        Salary::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/salaries');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'amount',
                        'date',
                        'active',
                        'payment_status',
                        'employee'
                    ]
                ]
            ]);
    }

    public function test_show_returns_salary_information()
    {
        $Salary = Salary::factory()->create();

        $response = $this->getJson("/api/v1/salaries/{$Salary->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'date',
                    'active',
                    'payment_status',
                    'employee',
                    'payments',
                ]
            ]);
    }

    public function test_store_creates_new_salary()
    {
        $Employee = Employee::factory()->create();
        $data = [
            'amount' => $this->faker->randomFloat(2, 1000, 5000),
            'date' => $this->faker->date(),
            'active' => $this->faker->boolean(),
            'employee_id' => $Employee->id,
        ];

        $response = $this->postJson('/api/v1/salaries', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'date',
                    'active',
                    'payment_status',
                ]
            ]);

        $this->assertDatabaseHas('salaries', [
            'amount' => $data['amount'],
            'date' => $data['date'],
        ]);

    }

    public function test_update_updates_existing_salary()
    {
        $Employee = Employee::factory()->create();
        $salary = Salary::factory()->create([
            'employee_id' => $Employee->id,
        ]);
        $data = [
            'amount' => $this->faker->randomFloat(2, 1000, 5000),
            'date' => $this->faker->date(),
            'active' => $this->faker->boolean(),
            'employee_id' => $Employee->id,
        ];

        $response = $this->putJson("/api/v1/salaries/{$salary->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'date',
                    'active',
                    'payment_status',
                ]
            ]);

        $this->assertDatabaseHas('salaries', [
            'id' => $salary->id,
            'amount' => $data['amount'],
            'date' => $data['date'],
        ]);
    }

    public function test_delete_should_not_return_salary()
    {
        $Salary = Salary::factory()->create();

        $response = $this->deleteJson("/api/v1/salaries/{$Salary->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/salaries/{$Salary->id}");
        $response->assertStatus(404)
            ->assertJson([
                'message' => "Service Error: can't find Salary"
            ]);
    }
}
