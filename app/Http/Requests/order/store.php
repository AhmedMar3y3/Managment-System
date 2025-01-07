<?php

namespace App\Http\Requests\order;

use Illuminate\Foundation\Http\FormRequest;

class store extends FormRequest
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
            'customer_name'    => ['required', 'string'],
            'customer_phone'   => ['required', 'string'],
            'customer_address' => ['required', 'string'],
            'order_type'       => ['nullable', 'string'],
            'order_details'    => ['required', 'string'],
            'price'            => ['required', 'numeric'],
            'deposit'          => ['nullable', 'numeric'],
            'delivery_date'    => ['required', 'date'],
            'notes'            => ['nullable', 'string'],
            'images'           => ['nullable', 'array'],
            'images.*'         => ['nullable', 'image','mimes:png,jpg,jpeg,gif','max:2048'],
        ];
    }
}
