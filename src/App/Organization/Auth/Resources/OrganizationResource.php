<?php

namespace App\Organization\Auth\Resources;

use Domain\Organization\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Organization $resource
 */
class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->getKey(),
            'name' => (string) $this->name,
            'contact_email' => $this->resource->contact_email,
            'website_url' => $this->resource->website_url,
            'address' => $this->resource->address,
            'number_of_employees' => (string) $this->resource->number_of_employees?->value,
            'industry' => $this->resource->industry,
        ];
    }
}
