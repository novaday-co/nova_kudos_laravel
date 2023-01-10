<?php

namespace App\Http\Resources\User\AccountInfo;

use Illuminate\Http\Resources\Json\JsonResource;

class DefaultCompanyUserResource extends JsonResource
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
            'company_id' => $this->company->id,
            'company_name' => $this->company->name,
            'company_avatar' => $this->checkAvatar($this->company->avatar),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'job_position' => $this->job_position,
            'avatar' => $this->checkAvatar($this->avatar),
            'coin_amount' => $this->coin_amount,
            'currency_amount' => $this->currency_amount,
           // 'notification_unread' => $this->notification_unread,
          //  'profile_complete' => boolType($this->profile_complete),
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
