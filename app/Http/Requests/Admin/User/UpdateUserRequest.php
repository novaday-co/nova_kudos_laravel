<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'mobile' => ['sometimes', 'required', 'string', 'min:11', 'max:64', 'unique:users,mobile', 'regex:/^[a-zA-Z0-9_.@\+]*$/'],
            'first_name' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'avatar' => ['sometimes', 'required', 'image', 'mimes:png,jpeg,jpg,gif,svg,webp']
        ];
    }
}
