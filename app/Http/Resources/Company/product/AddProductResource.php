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
      //      'transaction_detail' => UserProductTransactionResource::collection($this->companyUserProductTransactions),
           // 'user_coin_detail' => CompanyUserDetailResource::collection($this->companies)
          //  'coin_amount' => $this->pivot->coin_amount,
            'tracking_code' => $this->tracking_code,
            'coin_amount' => $this->user->defaultCompany->coin_amount
        ];
    }
}
