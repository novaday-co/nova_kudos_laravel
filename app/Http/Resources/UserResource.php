<?php

namespace App\Http\Resources;

use App\Http\Resources\User\AccountInfo\DefaultCompanyUserResource;
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
            'token' => $this->token ?? null,
            'super_admin' => $this->super_admin,
            // 'default_company' => DefaultCompanyUserResource::make($this->defaultCompany),
        ];
    }
}
