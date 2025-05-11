<?php

     namespace App\Http\Requests;

     use Illuminate\Foundation\Http\FormRequest;

     class SupplierRequest extends FormRequest
     {
         public function authorize(): bool
         {
             return true;
         }

         public function rules(): array
         {
             return [
                 'notes' => ['nullable', 'string'],
                 'balance' => ['nullable', 'numeric', 'decimal:0,2'],
                 'person_id' => ['required_without:person', 'exists:people,id'],
                 'person' => ['required_without:person_id', 'array'],
                 'person.name' => ['required_with:person', 'string', 'max:255'],
                 'person.phone_number' => ['required_with:person', 'string', 'max:20'],
                 'person.last_name' => ['nullable', 'string', 'max:255'],
                 'person.address' => ['nullable', 'string', 'max:255'],
                 'person.mail' => ['nullable', 'email', 'max:255', 'unique:people,mail'],
                 'person.dni' => ['nullable', 'string', 'max:20', 'unique:people,dni'],
                 'person.cuit' => ['nullable', 'string', 'max:20', 'unique:people,cuit'],
             ];
         }
     }
