<?php

    namespace App\Http\Requests\V1;

    use Illuminate\Foundation\Http\FormRequest;

    class ClientRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return [
                'balance' => ['nullable', 'numeric', 'min:0'],
                'person_id' => ['required_without:person', 'exists:people,id'],
                'person' => ['required_without:person_id', 'array'],
                'person.name' => ['required_with:person', 'string', 'max:255'],
                'person.last_name' => ['nullable', 'string', 'max:255'],
                'person.address' => ['nullable', 'string', 'max:255'],
                'person.phone_number' => ['required_with:person', 'string', 'max:20'],
                'person.mail' => ['nullable', 'email', 'max:255', 'unique:people,mail'],
                'person.dni' => ['nullable', 'string', 'max:20', 'unique:people,dni'],
                'person.cuit' => ['nullable', 'string', 'max:20', 'unique:people,cuit'],
            ];
        }

        public function messages(): array
        {
            return [
                'person_id.required_without_person' => 'Either person data or person ID is required',
                'person.required_without' => 'Either person data or person ID is required',
                'person.name.required_with' => 'The person name is required when creating a new person',
                'person.phone_number.required_with' => 'The phone number is required when creating a new person',
            ];
        }
    }
