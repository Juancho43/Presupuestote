<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SalaryRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:255'],
            'active' => ['boolean'],
            'employee_id' => ['required', 'exists:employees,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.decimal' => 'The amount must have 2 decimal places',
            'amount.min' => 'The amount must be greater than or equal to 0',
            'employee_id.required' => 'Employee ID is required',

        ];
    }
}
