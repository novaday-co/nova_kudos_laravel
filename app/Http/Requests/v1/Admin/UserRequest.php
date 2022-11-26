<?php

namespace App\Http\Requests\v1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'mobile' => ['required', 'string', 'min:11', 'max:64', 'unique:users,mobile', 'regex:/^[a-zA-Z0-9_.@\+]*$/'],
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'avatar' => ['required', 'image', 'mimes:png,jpeg,jpg,gif,svg,webp']
        ];
    }
}
