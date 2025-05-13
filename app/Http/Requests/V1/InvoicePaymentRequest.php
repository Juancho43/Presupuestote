<?php

namespace App\Http\Requests\V1;

class InvoicePaymentRequest extends PaymentRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'payable_type' => ['required', 'string', 'in:App\Models\Invoice'],
            'payable_id' => ['required', 'integer', 'exists:invoices,id']
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'payable_id.exists' => 'The selected invoice does not exist'
        ]);
    }
}
