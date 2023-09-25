<?php

namespace App\Organization\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthEmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => (int) $this->getKey(),
            'first_name'  => (string) $this->first_name,
            'last_name'  => (string) $this->last_name,
            'email'  => (string) $this->email,
            'is_organization_manager' => (bool) $this->is_organization_manager,
            'organization'  => OrganizationResource::make($this->whenLoaded('organization'))
        ];
    }
}
