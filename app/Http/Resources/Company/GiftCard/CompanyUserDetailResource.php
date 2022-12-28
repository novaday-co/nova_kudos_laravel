<?php

namespace App\Http\Resources\Company\GiftCard;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyUserDetailResource extends JsonResource
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
            'detail' => UserDetailResource::make($this->companies)
        ];
    }
}
