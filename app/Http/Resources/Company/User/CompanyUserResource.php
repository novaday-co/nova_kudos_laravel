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
            'company_name' => $this->name,
            'company_avatar' => $this->avatar,
            'first_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->first_name;
            }),
            'last_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->first_name;
            }),
            'avatar' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->first_name;
            }),
            'job_position' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->job_position;
            }),
            'currency_amount' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->currency_amount;
            }),
            'coin_amount' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->coin_amount;
            }),
            'roles' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->coin_amount;
            }),
        ];
    }
}
