<?php

namespace App\Http\Resources\Company\Coin;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyValueResource extends JsonResource
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
            'coin_value' => $this->coin_value,
            'system_value' => $this->system_value
        ];
    }
}
