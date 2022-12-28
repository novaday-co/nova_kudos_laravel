<?php

namespace App\Http\Resources\Company\GiftCard;

use App\Http\Resources\SuperAdmin\CompanyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SendGiftCardResource extends JsonResource
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
            'company_id' => $this->company->id,
            'company_name' => $this->company->name,
            'company_avatar' => $this->company->avatar,
           // 'companies' => new CompanyUserDetailResource($this->company),
            'from_id' => $this->fromId ? $this->fromId->mobile : $this->from_id,
            'to_id' => $this->toId ? $this->toId->mobile : $this->to_id,
            'message' => $this->message,
            'created_at' => dateTime($this->created_at),
            'updated_at' => dateTime($this->updated_at),
        ];
    }
}
