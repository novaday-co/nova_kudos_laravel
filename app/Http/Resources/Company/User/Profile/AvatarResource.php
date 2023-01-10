<?php

namespace App\Http\Resources\Company\User\Profile;

use Illuminate\Http\Resources\Json\JsonResource;

class AvatarResource extends JsonResource
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
           // 'user_mobile' => $this->user_id,
            'company_id' => $this->company_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'job_position' => $this->job_position,
            'avatar' => $this->checkAvatar($this->avatar),
            'coin_amount' => $this->coin_amount,
            'currency_amount' => $this->currency_amount,
            'notification_unread' =>$this->notification_unread,
            'role' => $this->role_id
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
