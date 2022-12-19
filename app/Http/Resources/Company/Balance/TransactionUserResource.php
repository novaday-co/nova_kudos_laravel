<?php

namespace App\Http\Resources\Company\Balance;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionUserResource extends JsonResource
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
            'user_mobile' => $this->user->mobile,
            'company' => $this->company->name,
            'amount' => $this->amount,
            'transaction_type' => exchangeTransactionType($this->transaction_type),
            'status' => exchangeStatus($this->status),
            'date' => dateTime($this->created_at)
        ];
    }
}
