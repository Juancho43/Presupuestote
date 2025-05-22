<?php

namespace App\Http\Requests\V1;

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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The measure name is required',
            'name.max' => 'The measure name cannot exceed 255 characters',
            'abbreviation.required' => 'The abbreviation is required',
            'abbreviation.max' => 'The abbreviation cannot exceed 10 characters',
        ];
    }
}
