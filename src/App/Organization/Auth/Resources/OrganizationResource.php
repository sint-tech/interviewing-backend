<?php

namespace App\Organization\Auth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->getKey(),
            'name' => (string) $this->name,
        ];
    }
}
