<?php

 namespace App\Http\Requests;

 use Illuminate\Foundation\Http\FormRequest;

 class SubCategoryRequest extends FormRequest
 {
     public function authorize(): bool
     {
         return true;
     }

     public function rules(): array
     {
         return [
             'name' => ['required', 'string', 'max:255'],
             'description' => ['nullable', 'string'],
             'category_id' => ['required', 'exists:categories,id']
         ];
     }

     public function messages(): array
     {
         return [
             'name.required' => 'The subcategory name is required',
             'name.max' => 'The subcategory name cannot exceed 255 characters',
             'category_id.required' => 'The parent category is required',
             'category_id.exists' => 'The selected parent category does not exist'
         ];
     }
 }
