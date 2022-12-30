<?php

namespace App\Http\Requests\Admin\Company\GiftCard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiftCardRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string'],
            'avatar' => ['sometimes', 'image', 'mimes:png,jpeg,jpg,gif,svg,webp'],
            'coin' => ['sometimes', 'required', 'integer'],
            'expiration_date' => ['sometimes', 'date']
        ];
    }
}
