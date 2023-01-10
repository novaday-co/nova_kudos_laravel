<?php

namespace App\Http\Resources\User\AccountInfo;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyInfoResource extends JsonResource
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
            'mobile' => $this->mobile,
            'default_company' => DefaultCompanyUserResource::make($this->defaultCompany)
        ];
    }
}
