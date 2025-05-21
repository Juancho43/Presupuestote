<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class AddMaterialsToInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
            return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'materials' => ['required', 'array'],
            'materials.*.id' => ['required', 'integer', 'exists:materials,id'],
            'materials.*.quantity' => ['required', 'integer', 'min:1'],
            'materials.*.price' => ['required', 'decimal:0,2'],
        ];
    }


}
