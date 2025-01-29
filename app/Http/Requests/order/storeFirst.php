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
            'order_type'       => ['required', 'string', 'in:كيك,ورد,كيك و ورد'],
            'order_details'    => ['nullable', 'string'],
            'flower_id'        => ['nullable', 'string', 'exists:flowers,id'],
            'flower_quantity'  => ['nullable', 'numeric', 'min:0'],
            'image'            => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
            'quantity'         => ['nullable', 'numeric', 'min:0'],
            'delivery_time'    => ['required', 'date_format:H:i'],
            'delivery_date'    => ['required', 'date'],
            'images'           => ['nullable', 'array'],
            'images.*'         => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
        ];

        if ($this->order_type === 'كيك') {
            $rules['flower_id'] = ['nullable', 'string', 'exists:flowers,id'];
            $rules['flower_quantity'] = ['nullable', 'numeric', 'min:0'];
            $rules['flower_image'] = ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'];
            $rules['order_details'] = ['required', 'string'];
            $rules['quantity'] = ['required', 'numeric', 'min:1'];
        } elseif ($this->order_type === 'ورد') {
            $rules['flower_id'] = ['required', 'string', 'exists:flowers,id'];
            $rules['flower_quantity'] = ['required', 'numeric', 'min:0'];
            $rules['flower_image'] = ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'];
            $rules['order_details'] = ['nullable', 'string'];
            $rules['quantity'] = ['nullable', 'numeric', 'min:0'];
        } elseif ($this->order_type === 'كيك و ورد') {
            $rules['flower_id'] = ['required', 'string', 'exists:flowers,id'];
            $rules['flower_quantity'] = ['required', 'numeric', 'min:0'];
            $rules['flower_image'] = ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'];
            $rules['order_details'] = ['required', 'string'];
            $rules['quantity'] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }
}
