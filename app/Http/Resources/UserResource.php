<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\Company\User\CompanyUserResource;

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
            'token' => $this->token,
            'super_admin' => $this->super_admin,
            'default_company' => $this->default_company,
            'companies' => CompanyUserResource::collection($this->companies),
        ];
    }
}
