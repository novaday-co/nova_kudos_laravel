<?php

namespace App\Http\Resources\Company\GiftCard;

use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'avatar' => $this->checkAvatar($this->avatar),
            'coin' => $this->coin,
            'company_name' => $this->company->name,
            'expiration_date' => $this->expiration_date,
        ];
    }

    private function checkAvatar($avatar = null)
    {
        if (!is_null($avatar)) {
            return asset('storage' . $avatar);
        }
        return null;
    }
}
