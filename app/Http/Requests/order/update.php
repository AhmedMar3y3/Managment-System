<?php

namespace App\Http\Requests\order;

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
                'customer_name'    => ['nullable', 'string'],
                'customer_phone'   => ['nullable', 'string'],
                'customer_address' => ['nullable', 'string'],
                'order_type'       => ['nullable', 'string'],
                'order_details'    => ['nullable', 'string'],
                'status'           => ['nullable', 'string'],
                'cake_price'            => ['nullable', 'numeric'],
                'deposit'          => ['nullable', 'numeric'],
                'delivery_date'    => ['nullable', 'date'],
                'notes'            => ['nullable', 'string'],
                'images'           => ['nullable', 'array'],
                'images.*'         => ['nullable', 'image','mimes:png,jpg,jpeg,gif','max:2048'],
            ];
    }
}
