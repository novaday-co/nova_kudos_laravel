<?php

namespace App\Http\Resources\Company\product;

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
            'coin_amount' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->coin_amount;
            }),
        ];
    }
}
