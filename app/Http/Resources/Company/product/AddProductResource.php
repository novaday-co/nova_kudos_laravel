<?php

namespace App\Http\Resources\Company\product;

use Illuminate\Http\Resources\Json\JsonResource;

class AddProductResource extends JsonResource
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
            'tracking_code' => $this->tracking_code,
            'coin_amount' => $this->user->defaultCompany->coin_amount
        ];
    }
}
