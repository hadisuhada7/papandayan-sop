<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePolicyLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['required', 'string', 'max:100'],
            'effective_date' => ['required', 'date'],
            'expired_date' => ['required', 'date', 'after_or_equal:effective_date'],
            'rules' => ['required', 'string', 'max:16777215'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['uuid', 'exists:categories,id'],
            'document_names' => ['nullable', 'array'],
            'document_names.*' => ['required_with:documents.*', 'string', 'max:255'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['required_with:document_names.*', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }
}
