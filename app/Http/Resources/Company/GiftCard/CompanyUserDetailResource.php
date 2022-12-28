<?php

namespace App\Http\Resources\Company\GiftCard;

use App\Http\Resources\UserResource;
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
            'company_name' => $this->name,
            'company_avatar' => $this->avatar,
            'user' => UserDetailResource::collection($this->users),
        ];
    }
}
