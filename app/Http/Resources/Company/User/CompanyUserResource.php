<?php

namespace App\Http\Resources\Company\User;

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
           // 'company_name' => $this->company_id,
           // 'company_avatar' => $this->avatar,
            'company_id' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->company_id;
            }),
            'first_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->first_name;
            }),
            'last_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->last_name;
            }),
            'job_position' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->job_position;
            }),
            'avatar' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->avatar;
            }),
            'coin_amount' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->coin_amount;
            }),
            'currency_amount' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->currency_amount;
            }),
            'notification_unread' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->notification_unread;
            }),
            'profile_complete' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->profile_complete;
            }),
            'roles' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->role_id;
            }),
        ];
    }
}
