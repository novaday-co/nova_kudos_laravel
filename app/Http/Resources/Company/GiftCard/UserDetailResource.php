<?php

namespace App\Http\Resources\Company\GiftCard;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            'first_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->first_name;
            }),
            'last_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->last_name;
            })
        ];
    }
}
