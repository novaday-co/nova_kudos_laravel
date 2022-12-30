<?php

namespace App\Http\Resources\SuperAdmin;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'company_avatar' => asset('storage' . $this->avatar),
        ];
    }
}
