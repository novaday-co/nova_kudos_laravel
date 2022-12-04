<?php

namespace App\Http\Resources\Admin;

use App\Models\GiftCard;
use Illuminate\Http\Resources\Json\JsonResource;

class SendGiftResource extends JsonResource
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
            'from' => $this->from_id,
            'to' =>$this->to_id,
            'giftCard' => $this->gift_id,
            'message' => $this->message,
        ];
    }
}
