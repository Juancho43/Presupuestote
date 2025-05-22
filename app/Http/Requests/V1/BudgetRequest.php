<?php

namespace App\Http\Requests\V1;

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
          'dead_line' => ['nullable', 'date', 'after:' . $this->input('made_date')],
          'profit' => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
          'client_id' => ['required', 'exists:clients,id'],
      ];
  }

  public function messages(): array
  {
      return [
          'made_date.required' => 'The made date is required',
          'made_date.date' => 'The made date must be a valid date',
          'description.required' => 'The description is required',
          'dead_line.date' => 'The deadline must be a valid date',
          'dead_line.after' => 'The deadline must be after the made date',
          'profit.decimal' => 'The profit must have up to 2 decimal places',
          'client_id.required' => 'The client ID is required',
          'client_id.exists' => 'The selected client does not exist',
      ];
    }
}
