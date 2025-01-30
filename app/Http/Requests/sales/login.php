<?php

namespace App\Http\Requests\sales;

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
            'login' =>'required|string',
            'password'=> 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'يجب إدخال البريد الإلكتروني أو رقم الهاتف',
            'password.required' => 'يجب إدخال كلمة المرور',
        ];
    }
}
