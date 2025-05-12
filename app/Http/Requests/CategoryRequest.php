<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required',
            'name.max' => 'The category name cannot exceed 255 characters',
        ];
    }
}
