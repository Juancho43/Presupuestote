<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $personId = $this->route('id'); // Gets the ID from the route parameter

        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'mail' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('people', 'mail')->ignore($personId)
            ],
            'dni' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('people', 'dni')->ignore($personId)
            ],
            'cuit' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('people', 'cuit')->ignore($personId)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The person name is required',
            'phone_number.required' => 'The phone number is required',
            'mail.unique' => 'This email is already registered',
            'dni.unique' => 'This DNI is already registered',
            'cuit.unique' => 'This CUIT is already registered',
        ];
    }
}
