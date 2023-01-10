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
            'company_id' => $this->defaultCompany->company->id,
            'company_name' => $this->defaultCompany->company->name,
            'company_avatar' => $this->checkAvatar($this->defaultCompany->company->avatar),
            'mobile' => $this->mobile,
            'first_name' => $this->defaultCompany?->first_name,
            'last_name' => $this->defaultCompany->last_name,
            'avatar' => $this->checkAvatar($this->defaultCompany->avatar),
            'job_position' => $this->defaultCompany?->job_position,
            'coin_amount' => $this->defaultCompany?->coin_amount,
            'currency_amount' => $this->defaultCompany?->currency_amount,

            //'default_company' => DefaultCompanyUserResource::make($this->defaultCompany)
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
