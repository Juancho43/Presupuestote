<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_id' => ['required', 'exists:materials,id'],
            'stock' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'date' => ['required', 'date']
        ];
    }

    public function messages(): array
    {
        return [
            'material_id.required' => 'The material is required',
            'material_id.exists' => 'The selected material does not exist',
            'stock.required' => 'The stock quantity is required',
            'stock.decimal' => 'The stock quantity must have 2 decimal places',
            'stock.min' => 'The stock quantity must be greater than or equal to 0',
            'date.required' => 'The date is required'
        ];
    }
}
