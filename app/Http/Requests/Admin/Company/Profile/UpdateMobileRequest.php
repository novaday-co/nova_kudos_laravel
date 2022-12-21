<?php

namespace App\Http\Requests\Admin\Company\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMobileRequest extends FormRequest
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
            'mobile' => ['required', 'string', 'digits:11', 'unique:users,mobile', 'regex:/^[a-zA-Z0-9_.@\+]*$/'],
        ];
    }
}
