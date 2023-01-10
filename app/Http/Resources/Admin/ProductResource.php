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
            'id' => $this->id,
            'company_name' => $this->company->name,
            'company_avatar' => $this->checkAvatar($this->company->avatar),
            'product_name' => $this->name,
            'product_currency' => $this->currency,
            'product_coin' => $this->coin,
            'product_amount' => $this->amount,
            'product_avatar' => $this->checkAvatar($this->avatar),
            'product_expiration_date' => $this->expiration_date,
            'product_created_at' => $this->created_at,
            'product_updated_at' => $this->updated_at,
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
