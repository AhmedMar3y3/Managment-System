<?php

namespace App\Http\Requests\manager;

use Illuminate\Foundation\Http\FormRequest;

class update extends FormRequest
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
            'first_name' => 'nullable|string',
            'last_name'  => 'nullable|string',
            'email' => 'nullable|email|unique:managers,email',
            'phone' => 'nullable|numeric|unique:managers,phone',
            'image'      => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'branch_id'  => 'nullable|exists:branches,id',
            'password'   => 'nullable|string',
        ];
    }
}
