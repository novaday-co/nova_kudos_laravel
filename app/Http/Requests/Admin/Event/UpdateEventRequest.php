<?php

namespace App\Http\Requests\Admin\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'required', 'integer'],
            'avatar' => ['sometimes', 'required', 'image', 'mimes:png,jpeg,jpg,gif,svg,webp'],
            'expiration_date' => ['sometimes', 'date']
        ];
    }
}
