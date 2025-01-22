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

        return [
            'order_type'       => ['required', 'string', 'in:كيك,ورد'],
            'order_details'    => ['required', 'string'],
            'quantity'         => ['required', 'numeric', 'min:1'],
            'delivery_date'    => ['required', 'date'],
            'images'           => ['nullable', 'array'],
            'images.*'         => ['nullable', 'image','mimes:png,jpg,jpeg,gif','max:2048'],
        
        ];
    }
}
