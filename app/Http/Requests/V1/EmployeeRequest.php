<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'salary' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['required', 'boolean'],
            'person_id' => ['required_without:person', 'exists:people,id'],
            'person' => ['required_without:person_id', 'array'],
            'person.name' => ['required_with:person', 'string', 'max:255'],
            'person.phone_number' => ['required_with:person', 'string', 'max:20'],
            'person.last_name' => ['nullable', 'string', 'max:255'],
            'person.address' => ['nullable', 'string', 'max:255'],
            'person.mail' => ['nullable', 'email', 'max:255', 'unique:people,mail'],
            'person.dni' => ['nullable', 'string', 'max:20', 'unique:people,dni'],
            'person.cuit' => ['nullable', 'string', 'max:20', 'unique:people,cuit'],
        ];
    }
    /**
     * Custom messages for validation rules
     *
     * @return array
     */

    public function messages(): array
    {
        return [
            'salary.required' => 'The salary is required',
            'salary.numeric' => 'The salary must be a number',
            'salary.decimal' => 'The salary must have up to 2 decimal places',
            'salary.min' => 'The salary must be at least 0',
            'start_date.required' => 'The start date is required',
            'start_date.date' => 'The start date must be a valid date',
            'end_date.date' => 'The end date must be a valid date',
            'end_date.after' => 'The end date must be after the start date',
            'is_active.required' => 'The active status is required',
            'is_active.boolean' => 'The active status must be true or false',
            'person_id.required_without' => 'Either person data or person ID is required',
            'person_id.exists' => 'The selected person does not exist',
            'person.required_without' => 'Either person data or person ID is required',
            'person.name.required_with' => 'The person name is required when creating a new person',
            'person.phone_number.required_with' => 'The phone number is required when creating a new person',
            'person.mail.email' => 'The email must be a valid email address',
            'person.mail.unique' => 'This email is already in use',
            'person.dni.unique' => 'This DNI is already in use',
            'person.cuit.unique' => 'This CUIT is already in use',
        ];
    }

}
