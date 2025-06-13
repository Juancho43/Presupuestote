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
            'unit_measure' => ['required', 'numeric', 'min:0'],
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
            'measure_id' => ['required', 'exists:measures,id'],
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
            'sub_category_id.required' => 'The sub-category is required',
            'sub_category_id.exists' => 'The selected sub-category does not exist',
        ];
    }
}
