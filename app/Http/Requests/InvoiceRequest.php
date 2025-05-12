<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'supplier_id' => ['required_without:person', 'exists:suppliers,id'],
            'person' => ['required_without:supplier_id', 'array'],
            'person.name' => ['required_with:person', 'string'],
            'person.phone_number' => ['required_with:person', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required_without' => 'Either supplier ID or person details are required',
            'person.required_without' => 'Either supplier ID or person details are required',
            'person.name.required_with' => 'Person name is required when providing person details',
            'person.phone_number.required_with' => 'Person phone number is required when providing person details'
        ];
    }
}
