<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_payment_list(): void
    {
        Payment::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'amount',
                        'date',
                        'description',
                        'payable_type',
                        'payable_id',
                    ]
                ]
            ]);
    }

    public function test_show_returns_payment_information()
    {
        $payment = Payment::factory()->create();

        $response = $this->getJson("/api/v1/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'date',
                    'description',
                    'payable_type',
                    'payable_id',
                ]
            ]);
    }

    public function test_store_creates_new_payment()
    {
        $invoice = Invoice::factory()->create([
            'total' => 1000
        ]);


        $data = [
           'amount' => $this->faker->randomFloat(2, 10, 1000),
           'date' => $this->faker->date(),
           'description' => $this->faker->sentence(),
           'payable_type' => 'App\\Models\\Invoice',
           'payable_id' => $invoice->id,
        ];

        $response = $this->postJson('/api/v1/payments', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'date',
                    'description',
                    'payable_type',
                    'payable_id',
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'amount' => $data['amount'],
            'date' => $data['date'],
            'description' => $data['description'],
            'payable_type' => $data['payable_type'],
            'payable_id' => $data['payable_id'],
        ]);

    }

    public function test_update_updates_existing_payment()
    {
        $invoice = Invoice::factory()->create([
            'total' => 1000
        ]);

        $Payment = Payment::factory()->create([
            'payable_type' => 'App\\Models\\Invoice',
            'payable_id' => $invoice->id,
        ]);
        $data = [
            'amount' => $this->faker->randomFloat(2, 10, 200),
            'date' => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'payable_type' => 'App\\Models\\Invoice',
            'payable_id' => $invoice->id,
        ];


        $response = $this->putJson("/api/v1/payments/{$Payment->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'date',
                    'description',
                    'payable_type',
                    'payable_id',
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $Payment->id,
            'amount' => $data['amount'],
            'date' => $data['date'],
            'description' => $data['description'],
            'payable_type' => $data['payable_type'],
            'payable_id' => $data['payable_id'],
        ]);
    }

    public function test_delete_should_not_return_payment()
    {
        $Payment = Payment::factory()->create();

        $response = $this->deleteJson("/api/v1/payments/{$Payment->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/payments/{$Payment->id}");
        $response->assertStatus(404)
            ->assertJson([
                'message' => "Service Error: can't find Payment"
            ]);
    }
}
