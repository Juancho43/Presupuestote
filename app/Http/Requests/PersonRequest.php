<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'mail' => ['nullable', 'email', 'max:255', 'unique:people,mail'],
            'dni' => ['nullable', 'string', 'max:20', 'unique:people,dni'],
            'cuit' => ['nullable', 'string', 'max:20', 'unique:people,cuit'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name is required',
            'mail.email' => 'Please provide a valid email address',
            'mail.unique' => 'This email is already registered',
            'dni.unique' => 'This DNI is already registered',
            'cuit.unique' => 'This CUIT is already registered',
        ];
    }
}
