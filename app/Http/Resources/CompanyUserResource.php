<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyUserResource extends JsonResource
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
            'user_id' => $this->companies->first->user_id,
            'company_id' => $this->company_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'job_position' => $this->job_position,
            'avatar' => $this->avatar,
            'coin_amount' => $this->coin_amount,
            'currency_amount' => $this->currency_amount,
            'notification_unread' =>$this->notification_unread,
            'role' => $this->role_id
        ];
    }
}
