<?php

    namespace App\Http\Requests\V1;

    use Illuminate\Foundation\Http\FormRequest;

    class ClientUpdateRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            // Reutiliza las reglas de PersonUpdateRequest
            return [
                'balance' => ['nullable', 'numeric', 'min:0'],
                'person'=>(new PersonUpdateRequest())->rules(),
            ];
        }
    }
