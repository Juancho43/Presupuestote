<?php

namespace App\Http\Requests\V1;

class EmployeeUpdateRequest extends PersonUpdateRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Add employee-specific rules

        $rules['salary'] = ['nullable', 'numeric', 'min:0'];
        $rules['start_date'] = ['nullable', 'date'];
        $rules['end_date'] = ['nullable', 'date', 'after:start_date'];
        $rules['is_active'] = ['boolean'];

        return $rules;
    }

    public function messages(): array
    {
        $messages = parent::messages();

        // Add employee-specific messages
        $messages['salary.numeric'] = 'The salary must be a number';
        $messages['salary.min'] = 'The salary cannot be negative';
        $messages['end_date.after'] = 'The end date must be after the start date';

        return $messages;
    }
}
