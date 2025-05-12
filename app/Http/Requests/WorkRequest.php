<?php

namespace App\Http\Requests;

use App\Enums\WorkStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class WorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'estimated_time' => ['required', 'integer', 'min:0'],
            'dead_line' => ['required', 'date'],
            'cost' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'status' => ['required', new Enum(WorkStatus::class)],
            'budget_id' => ['required', 'exists:budgets,id']
        ];
    }

    public function messages(): array
    {
        return [
            'order.required' => 'The work order is required',
            'order.integer' => 'The work order must be a number',
            'name.required' => 'The work name is required',
            'name.max' => 'The work name cannot exceed 255 characters',
            'estimated_time.required' => 'The estimated time is required',
            'estimated_time.min' => 'The estimated time must be greater than or equal to 0',
            'dead_line.required' => 'The deadline is required',
            'dead_line.date' => 'The deadline must be a valid date',
            'cost.required' => 'The cost is required',
            'cost.decimal' => 'The cost must have 2 decimal places',
            'cost.min' => 'The cost must be greater than or equal to 0',
            'status.required' => 'The status is required',
            'budget_id.required' => 'The budget is required',
            'budget_id.exists' => 'The selected budget does not exist'
        ];
    }
}
