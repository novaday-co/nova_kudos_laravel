<?php

namespace App\Http\Requests\Admin\Company\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string'],
            'currency' => ['sometimes', 'required', 'integer'],
            'amount' => ['sometimes', 'required', 'integer'],
            'avatar' => ['sometimes', 'image', 'mimes:png,jpeg,jpg,gif,svg,webp'],
            'expiration_date' => ['sometimes', 'date', 'after:today'],
        ];
    }
}
