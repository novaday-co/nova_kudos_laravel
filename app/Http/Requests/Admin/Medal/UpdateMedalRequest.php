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
            'title' => ['sometimes', 'required', 'string'],
            'icon' => ['sometimes', 'image', 'mimes:png,jpeg,jpg,gif,svg,webp'],
            'score' => ['sometimes', 'numeric', 'integer']
        ];
    }
}
