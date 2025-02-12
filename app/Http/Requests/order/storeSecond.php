<?php

namespace App\Http\Requests\order;

use Illuminate\Foundation\Http\FormRequest;

class storeSecond extends FormRequest
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
            'cake_price'     => ['nullable','numeric'],
            'flower_price'   => ['nullable','numeric'],
            'deposit'        => ['nullable','numeric'],
            'delivery_price' => ['required','numeric'],
        ];
    }
}
