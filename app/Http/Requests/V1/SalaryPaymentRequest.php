<?php

namespace App\Http\Requests\V1;

class SalaryPaymentRequest extends PaymentRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'payable_type' => ['required', 'string', 'in:App\Models\Salary'],
            'payable_id' => ['required', 'integer', 'exists:salaries,id']
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'payable_id.exists' => 'The selected salary does not exist'
        ]);
    }
}
