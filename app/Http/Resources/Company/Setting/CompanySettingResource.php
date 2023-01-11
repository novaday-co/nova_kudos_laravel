<?php

namespace App\Http\Resources\Company\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanySettingResource extends JsonResource
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
            'company_id' => $this->id,
            'company_name' => $this->name,
            'withdrawal permission' => $this->withdrawal_permission,
            'minimum withdrawal' => $this->min_withdrawal,
            'maximum withdrawal' => $this->max_withdrawal,
        ];
    }
}
