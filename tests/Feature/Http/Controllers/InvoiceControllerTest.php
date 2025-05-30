<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_invoices_list(): void
    {
        Invoice::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/invoices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'date',
                        'total',
                        'payment_status',
                        'supplier' => [
                            'id',
                            'notes',
                            'person' => [
                                'id',
                                'name',
                                'address',
                                'phone_number',
                                'mail'
                            ]
                        ],
                    ]
                ]
            ]);
    }

    public function test_show_returns_invoice_information()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->getJson("/api/v1/invoices/{$invoice->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'date',
                    'total',
                    'supplier' => [
                        'id',
                        'notes',
                        'person' => [
                            'id',
                            'name',
                            'address',
                            'phone_number',
                            'mail'
                        ]
                    ],
                    'materials'
                ]
            ]);
    }

    public function test_store_creates_new_invoice()
    {
        $supplier = Supplier::factory()->create();
        $data = [
            'date' => now()->format('Y-m-d'),
            'supplier_id' => $supplier->id
        ];

        $response = $this->postJson('/api/v1/invoices', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'date',
                    'total',
                ]
            ]);

        $this->assertDatabaseHas('invoices', [
            'supplier_id' => $supplier->id
        ]);
    }

    public function test_update_updates_existing_invoice()
    {
        $invoice = Invoice::factory()->create();
        $newSupplier = Supplier::factory()->create();

        $data = [
            'date' => now()->format('Y-m-d'),
            'supplier_id' => $newSupplier->id
        ];

        $response = $this->putJson("/api/v1/invoices/{$invoice->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'date',
                    'total',
                ]
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'supplier_id' => $newSupplier->id
        ]);
    }

    public function test_delete_should_not_return_invoice()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->deleteJson("/api/v1/invoices/{$invoice->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/invoices/{$invoice->id}");
        $response->assertStatus(500)
            ->assertJson([
                'message' => "Service Error: can't find Invoice"
            ]);
    }

    public function test_update_invoice_total()
    {
        $invoice = Invoice::factory()->create();
        $material = Material::factory()->create();

        // Add material to invoice
        $this->postJson('/api/v1/invoices/materials', [
            'invoice_id' => $invoice->id,
            'materials' => [
                [
                    'id' => $material->id,
                    'quantity' => 2,
                    'price' => 100
                ]
            ]
        ]);

        $response = $this->getJson("/api/v1/invoices/updateTotal/{$invoice->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'total'
                ]
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'total' => 200 // 2 * 100
        ]);
    }

    public function test_add_materials_to_invoice()
    {
        $invoice = Invoice::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'invoice_id' => $invoice->id,
            'materials' => [
                [
                    'id' => $material->id,
                    'quantity' => 2,
                    'price' => 100
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/invoices/materials', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'materials' => [
                        '*' => [
                            'id',
                            'quantity',
                            'price'
                        ]
                    ]
                ]
            ]);

    }
}
