<?php

namespace App\Http\Requests\Admin\Company\Setting;

use Illuminate\Foundation\Http\FormRequest;

class CompanySettingRequest extends FormRequest
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
            'withdrawal_permission' => ['required', 'in:enable,disable'],
            'min_withdrawal' => ['required', 'numeric', 'integer'],
            'max_withdrawal' => ['required', 'numeric', 'integer']
        ];
    }
}
