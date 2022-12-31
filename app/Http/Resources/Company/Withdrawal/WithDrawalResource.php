<?php

namespace App\Http\Resources\Company\Withdrawal;

use Illuminate\Http\Resources\Json\JsonResource;

class WithDrawalResource extends JsonResource
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
            'currency_amount' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->currency_amount;
            }),
        ];
    }
}
