<?php

namespace App\Http\Requests\V1;

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
         'description' => ['nullable', 'string'],
         'payable_id' => ['required', 'integer', 'min:1'],
         'payable_type' => ['required', 'string', 'in:App\\Models\\Budget,App\\Models\\Invoice,App\\Models\\Salary']
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
         'date.date' => 'The payment date must be a valid date',
         'payable_id.required' => 'The payable ID is required',
         'payable_id.integer' => 'The payable ID must be an integer',
         'payable_id.min' => 'The payable ID must be greater than or equal to 1',
         'payable_type.required' => 'The payable type is required',
         'payable_type.string' => 'The payable type must be a string',
         'payable_type.in' => 'The payable type must be a valid model type'
     ];
 }
}
