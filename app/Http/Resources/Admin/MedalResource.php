<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MedalResource extends JsonResource
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
            'id' => $this->id,
            'medal_name' => $this->name,
            'medal_avatar' => $this->checkAvatar($this->avatar),
            'medal_coin' => $this->coin,
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
