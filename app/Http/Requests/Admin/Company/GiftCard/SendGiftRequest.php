<?php

namespace App\Http\Requests\Admin\Company\GiftCard;

use Illuminate\Foundation\Http\FormRequest;

class SendGiftRequest extends FormRequest
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
            'to_id' => ['required', 'integer', 'exists:company_user,user_id'],
            'gift_id' => ['required', 'integer', 'exists:gift_cards,id'],
            'message' => ['string']
        ];
    }
}
