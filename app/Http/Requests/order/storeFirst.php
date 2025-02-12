<?php

namespace App\Http\Requests\order;

use Illuminate\Foundation\Http\FormRequest;

class storeFirst extends FormRequest
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

        $rules = [
            "is_sameday"       => ['required', 'boolean'],
            'order_type'       => ['required', 'string', 'in:cake,flower,cake and flower'],
            'order_details'    => ['nullable', 'string'],
            'delivery_time'    => ['required', 'date_format:H:i'],
            'delivery_date'    => ['required', 'date'],
            'image'            => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
            'description'      => ['nullable','string'],
            'images'           => ['nullable', 'array'],
            'images.*'         => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
        ];

        if ($this->is_sameday) {
            $rules['delivery_date'] = ['nullable', 'date'];
            $rules['delivery_time'] = ['nullable', 'date_format:H:i'];

        }
         return $rules;
    }
}



