<?php

namespace App\Http\Requests\V1;

use App\Enums\WorkStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AddWorksToBudgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'budget_id' => ['required', 'exists:budgets,id'],
            'work_ids' => ['required', 'array'],
            'work_ids.*' => ['exists:works,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'budget_id.required' => 'The budget ID is required',
            'budget_id.exists' => 'The selected budget does not exist',
            'work_ids.required_without' => 'Either existing work IDs are required',

        ];
    }
}
