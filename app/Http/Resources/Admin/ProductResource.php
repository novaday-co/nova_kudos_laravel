<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'company_name' => $this->company->name,
            'company_avatar' => $this->company->avatar,
            'product_name' => $this->name,
            'product_currency' => $this->currency,
            'product_amount' => $this->amount,
            'product_avatar' => $this->avatar,
            'product_expiration_date' => $this->expiration_date,
        ];
    }
}
