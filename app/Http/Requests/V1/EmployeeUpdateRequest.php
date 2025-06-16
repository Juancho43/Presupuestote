<?php

namespace App\Http\Requests\V1;

class EmployeeUpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        // Add employee-specific rules
        return [
            'balance' => ['nullable', 'numeric', 'min:0'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
            'person'=>(new PersonUpdateRequest())->rules(),
        ];
    }


}
