<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function __construct($resource, private $token = null)
    {
        parent::__construct($resource);
    }

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
            'login_count' => $this->login_count,
            'first_name' => $this->whenPivotLoaded('company_user', function (){
                return $this->pivot->first_name;
            }),
            'last_name' => $this->last_name,
            'job_position' => $this->job_title,
            'coin_amount' => $this->coin_amount,
            'currency_amount' => $this->currency_amount,
            'notification_unread' => $this->notification_unread,
            'role_id' => $this->role_id,
            'is_default' => $this->is_default,
            'avatar' => $this->avatar,
            'token' => $this->token
        ];
    }
}
