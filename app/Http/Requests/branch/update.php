<?php

namespace App\Http\Requests\branch;

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
            'name'    => 'nullable|string',
            'phone'   => 'nullable|string',
            'address' => 'nullable|string',
            'long'    => 'nullable|string',
            'lat'    => 'nullable|string',
        ];
    }
}
