<?php

namespace App\Http\Requests\Admin\Medal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedalRequest extends FormRequest
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
            'avatar' => ['nullable', 'mimes:png,jpeg,jpg,gif,svg,webp'],
            'coin' => ['sometimes', 'numeric', 'integer']
        ];
    }
}
