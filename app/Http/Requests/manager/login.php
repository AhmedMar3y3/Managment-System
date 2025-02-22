<?php

namespace App\Http\Requests\manager;

use Illuminate\Foundation\Http\FormRequest;

class login extends FormRequest
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
            'password' =>'required|string',
            'login' =>'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'The email or phone number is required.',
            'password.required' => 'The password is required.',
        ];
    }
}

