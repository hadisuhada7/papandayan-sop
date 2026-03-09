<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserDetailRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:75'],
            'email' => ['required', 'string', 'email', 'max:50', Rule::unique('users')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'min:6', 'max:25'],
            'category_user' => ['required', 'in:admin,viewer'],
            'company_id' => ['required', 'string', 'exists:companies,id'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['uuid', 'exists:categories,id'],
        ];
    }
}
