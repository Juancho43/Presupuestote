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
            'active' => ['boolean'],
            'employee_id' => ['required_without_all:person.name,person.phone_number', 'exists:employees,id'],
            'person.name' => ['required_without:employee_id', 'string', 'required_with:person.phone_number'],
            'person.phone_number' => ['required_without:employee_id', 'string', 'required_with:person.name'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.decimal' => 'The amount must have 2 decimal places',
            'amount.min' => 'The amount must be greater than or equal to 0',
            'employee_id.required_without_all' => 'Either employee ID or person details are required',
            'person.name.required_without' => 'Person name is required when employee ID is not provided',
            'person.phone_number.required_without' => 'Person phone number is required when employee ID is not provided'
        ];
    }
}
