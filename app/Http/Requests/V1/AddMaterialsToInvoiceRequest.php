<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class AddMaterialsToInvoiceRequest extends FormRequest
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
            'materials' => ['required', 'array', 'min:1'],
            'materials.*.id' => ['required', 'exists:materials,id'],
            'materials.*.quantity' => ['required', 'numeric', 'min:0'],
            'materials.*.unit_price' => ['required', 'numeric', 'decimal:0,2', 'min:0']
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required_without_all' => 'Either supplier ID or person details are required',
            'person.name.required_without' => 'Person name is required when supplier ID is not provided',
            'person.phone_number.required_without' => 'Person phone number is required when supplier ID is not provided',
            'materials.required' => 'At least one material is required',
            'materials.*.quantity.min' => 'Material quantity must be greater than 0',
            'materials.*.unit_price.min' => 'Material unit price must be greater than 0'
        ];
    }
}
