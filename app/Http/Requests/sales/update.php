<?php

namespace App\Http\Requests\sales;

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
            'email'      => 'nullable|email|unique:sales,email',
            'phone'      => 'nullable|numeric|unique:sales,phone',
            'image'      => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'password'   => 'nullable|string',
        ];
    }
}
