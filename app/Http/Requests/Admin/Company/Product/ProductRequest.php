<?php

namespace App\Http\Requests\Admin\Company\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'currency' => ['required', 'integer'],
            'amount' => ['required', 'integer'],
            'avatar' => ['nullable', 'mimes:png,jpeg,jpg,gif,svg,webp'],
            'expiration_date' => ['sometimes', 'date', 'after:today'],
        ];
    }
}
