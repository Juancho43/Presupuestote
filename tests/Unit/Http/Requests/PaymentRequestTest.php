<?php

    namespace Tests\Unit\Http\Requests;

    use App\Http\Requests\V1\BudgetPaymentRequest;
    use App\Http\Requests\V1\InvoicePaymentRequest;
    use App\Http\Requests\V1\SalaryPaymentRequest;
    use App\Models\Budget;
    use App\Models\Invoice;
    use App\Models\Salary;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Support\Facades\Validator;
    use Tests\TestCase;

    class PaymentRequestTest extends TestCase
    {
        use RefreshDatabase;

        public function test_valid_invoice_payment_passes_validation()
        {
            $invoice = Invoice::factory()->create();
            $rules = (new InvoicePaymentRequest())->rules();

            $validator = Validator::make([
                'amount' => '100.50',
                'date' => '2024-03-20',
                'description' => 'Test payment',
                'payable_id' => $invoice->id,
                'payable_type' => 'App\Models\Invoice'
            ], $rules);

            $this->assertFalse($validator->fails());
        }

        public function test_valid_budget_payment_passes_validation()
        {
            $budget = Budget::factory()->create();
            $rules = (new BudgetPaymentRequest())->rules();

            $validator = Validator::make([
                'amount' => '100.50',
                'date' => '2024-03-20',
                'description' => 'Test payment',
                'payable_id' => $budget->id,
                'payable_type' => 'App\Models\Budget'
            ], $rules);

            $this->assertFalse($validator->fails());
        }

        public function test_valid_salary_payment_passes_validation()
        {
            $salary = Salary::factory()->create();
            $rules = (new SalaryPaymentRequest())->rules();

            $validator = Validator::make([
                'amount' => '100.50',
                'date' => '2024-03-20',
                'description' => 'Test payment',
                'payable_id' => $salary->id,
                'payable_type' => 'App\Models\Salary'
            ], $rules);

            $this->assertFalse($validator->fails());
        }

        public function test_fails_validation_with_invalid_amount()
        {
            $invoice = Invoice::factory()->create();
            $rules = (new InvoicePaymentRequest())->rules();

            $validator = Validator::make([
                'amount' => '-100.50',
                'date' => '2024-03-20',
                'payable_id' => $invoice->id,
                'payable_type' => 'App\Models\Invoice'
            ], $rules);

            $this->assertTrue($validator->fails());
            $this->assertArrayHasKey('amount', $validator->errors()->toArray());
        }

        public function test_fails_validation_with_invalid_date()
        {
            $invoice = Invoice::factory()->create();
            $rules = (new InvoicePaymentRequest())->rules();

            $validator = Validator::make([
                'amount' => '100.50',
                'date' => 'invalid-date',
                'payable_id' => $invoice->id,
                'payable_type' => 'App\Models\Invoice'
            ], $rules);

            $this->assertTrue($validator->fails());
            $this->assertArrayHasKey('date', $validator->errors()->toArray());
        }

        public function test_fails_validation_with_nonexistent_model()
        {
            $rules = (new InvoicePaymentRequest())->rules();

            $validator = Validator::make([
                'amount' => '100.50',
                'date' => '2024-03-20',
                'payable_id' => 999,
                'payable_type' => 'App\Models\Invoice'
            ], $rules);

            $this->assertTrue($validator->fails());
            $this->assertArrayHasKey('payable_id', $validator->errors()->toArray());
        }
    }
