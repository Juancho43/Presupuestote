<?php

namespace App\Http\Requests;

use App\Enums\BudgetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'made_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'dead_line' => ['required', 'date', 'after:made_date'],
            'status' => ['required', new Enum(BudgetStatus::class)],
            'cost' => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'client_id' => ['required_without_all:person.name,person.phone_number', 'exists:clients,id'],
            'person.name' => ['required_without:client_id', 'string', 'required_with:person.phone_number'],
            'person.phone_number' => ['required_without:client_id', 'string', 'required_with:person.name'],
        ];
    }

    public function messages(): array
    {
        return [
            'dead_line.after' => 'The deadline must be after the made date',
            'client_id.required_without_all' => 'Either client ID or person details are required',
            'person.name.required_without' => 'Person name is required when client ID is not provided',
            'person.phone_number.required_without' => 'Person phone number is required when client ID is not provided',
        ];
    }
}
