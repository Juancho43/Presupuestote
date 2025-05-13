<?php

namespace App\Http\Requests\V1;

class BudgetPaymentRequest extends PaymentRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'payable_type' => ['required', 'string', 'in:App\Models\Budget'],
            'payable_id' => ['required', 'integer', 'exists:budgets,id']
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'payable_id.exists' => 'The selected budget does not exist'
        ]);
    }
}
