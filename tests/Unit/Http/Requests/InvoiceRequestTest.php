<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\V1\InvoiceRequest;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

// Changed from AddMaterialsToInvoiceRequest

class InvoiceRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRequest(): array
    {
        $request = new InvoiceRequest();  // Changed from AddMaterialsToInvoiceRequest
        return $request->rules();
    }

    public function test_valid_invoice_with_supplier_passes_validation()
    {
        $supplier = Supplier::factory()->create();

        $validator = Validator::make([
            'date' => '2024-03-20',
            'supplier_id' => $supplier->id
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_valid_invoice_with_person_details_passes_validation()
    {
        $validator = Validator::make([
            'date' => '2024-03-20',
            'person' => [
                'name' => 'John Doe',
                'phone_number' => '1234567890'
            ]
        ], $this->getRequest());

        $this->assertFalse($validator->fails());
    }

    public function test_fails_validation_without_date()
    {
        $supplier = Supplier::factory()->create();

        $validator = Validator::make([
            'supplier_id' => $supplier->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }

    public function test_fails_validation_without_supplier_or_person()
    {
        $validator = Validator::make([
            'date' => '2024-03-20'
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
    }

    public function test_fails_validation_with_non_existent_supplier()
    {
        $validator = Validator::make([
            'date' => '2024-03-20',
            'supplier_id' => 999
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('supplier_id', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_incomplete_person_details()
    {
        $validator = Validator::make([
            'date' => '2024-03-20',
            'person' => [
                'name' => 'John Doe'
            ]
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('person.phone_number', $validator->errors()->toArray());
    }

    public function test_fails_validation_with_invalid_date()
    {
        $supplier = Supplier::factory()->create();

        $validator = Validator::make([
            'date' => 'invalid-date',
            'supplier_id' => $supplier->id
        ], $this->getRequest());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }
}
