<?php

namespace App\Http\Requests\manager;

use Illuminate\Foundation\Http\FormRequest;

class register extends FormRequest
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
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:managers,email',
            'phone'      => 'required|numeric|unique:managers,phone',
            'image'      => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'password'   => 'required|string',
            'branch_id'=>'required|exists:branches,id',
    
            
        ];
    }
}
