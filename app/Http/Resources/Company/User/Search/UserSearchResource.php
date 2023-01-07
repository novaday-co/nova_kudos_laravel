<?php

namespace App\Http\Resources\Company\User\Search;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSearchResource extends JsonResource
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
            // 'user_detail' => CompanyUserSearchResource::collection($this->companies),
            'first_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->first_name;
            }),
            'last_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->last_name;
            }),
            'avatar' => $this->whenPivotLoaded('company_user', function (){
                return asset('storage' . $this->pivot->avatar);
            }),
        ];
    }
}
