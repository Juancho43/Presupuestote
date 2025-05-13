<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:50'],
            'brand' => ['nullable', 'string', 'max:100'],
            'measure_id' => ['required', 'exists:measures,id'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The material name is required',
            'name.max' => 'The material name cannot exceed 255 characters',
            'color.max' => 'The color cannot exceed 50 characters',
            'brand.max' => 'The brand cannot exceed 100 characters',
            'measure_id.required' => 'The measure is required',
            'measure_id.exists' => 'The selected measure does not exist',
            'stock.min' => 'The stock quantity must be greater than or equal to 0',
            'unit_price.decimal' => 'The unit price must have 2 decimal places',
            'unit_price.min' => 'The unit price must be greater than or equal to 0'
        ];
    }
}
