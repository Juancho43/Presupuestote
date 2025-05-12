<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'The payment amount is required',
            'amount.numeric' => 'The payment amount must be a number',
            'amount.decimal' => 'The payment amount must have 2 decimal places',
            'amount.min' => 'The payment amount must be greater than or equal to 0',
            'date.required' => 'The payment date is required',
            'date.date' => 'The payment date must be a valid date'
        ];
    }
}
