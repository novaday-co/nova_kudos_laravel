<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'mobile' => ['sometimes', 'required', 'string', 'max:11', 'unique:users,mobile', 'regex:/^[a-zA-Z0-9_.@\+]*$/'],
            'avatar' => ['sometimes', 'image', 'mimes:png,jpeg,jpg,gif,svg,webp']
        ];
    }
}
