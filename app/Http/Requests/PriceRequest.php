<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_id' => ['required', 'exists:materials,id'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'date' => ['required', 'date']
        ];
    }

    public function messages(): array
    {
        return [
            'material_id.required' => 'The material is required',
            'material_id.exists' => 'The selected material does not exist',
            'price.required' => 'The price is required',
            'price.decimal' => 'The price must have 2 decimal places',
            'price.min' => 'The price must be greater than or equal to 0',
            'date.required' => 'The date is required'
        ];
    }
}
