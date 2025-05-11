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
            'supplier_id' => ['required_without_all:person.name,person.phone_number', 'exists:suppliers,id'],
            'person.name' => ['required_without:supplier_id', 'string', 'required_with:person.phone_number'],
            'person.phone_number' => ['required_without:supplier_id', 'string', 'required_with:person.name'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required_without_all' => 'Either supplier ID or person details are required',
            'person.name.required_without' => 'Person name is required when supplier ID is not provided',
            'person.phone_number.required_without' => 'Person phone number is required when supplier ID is not provided'
        ];
    }
}
