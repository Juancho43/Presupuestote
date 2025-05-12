<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeasureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:10'],
            'unit' => ['required', 'numeric', 'decimal:0,2', 'min:0']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The measure name is required',
            'name.max' => 'The measure name cannot exceed 255 characters',
            'abbreviation.required' => 'The abbreviation is required',
            'abbreviation.max' => 'The abbreviation cannot exceed 10 characters',
            'unit.decimal' => 'The unit must have 2 decimal places',
            'unit.min' => 'The unit must be greater than or equal to 0'
        ];
    }
}
