<?php

namespace App\Http\Requests\order;

use Illuminate\Foundation\Http\FormRequest;

class storeThird extends FormRequest
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
            'customer_name'  => ['required', 'string'],
            'customer_phone' => ['required', 'string'],
            'longitude'      => ['required', 'string'],
            'latitude'       => ['required', 'string'],
            'map_desc'       => ['required', 'string'],
            'additional_data'=> ['nullable', 'string'],
        ];
    }
}
