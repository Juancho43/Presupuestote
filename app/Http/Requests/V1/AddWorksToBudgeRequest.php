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
            'work_ids' => ['required_without:works', 'array'],
            'work_ids.*' => ['exists:works,id'],

            'works' => ['required_without:work_ids', 'array'],
            'works.*.name' => ['required_with:works', 'string'],
            'works.*.notes' => ['nullable', 'string'],
            'works.*.estimated_time' => ['nullable', 'integer'],
            'works.*.dead_line' => ['required_with:works', 'date'],
            'works.*.cost' => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'works.*.status' => ['required_with:works', new Enum(WorkStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'budget_id.required' => 'The budget ID is required',
            'budget_id.exists' => 'The selected budget does not exist',
            'work_ids.required_without' => 'Either existing work IDs or new work details are required',
            'works.required_without' => 'Either existing work IDs or new work details are required',
            'works.*.dead_line.date' => 'The deadline must be a valid date',
            'works.*.cost.decimal' => 'The cost must have 2 decimal places',
        ];
    }
}
