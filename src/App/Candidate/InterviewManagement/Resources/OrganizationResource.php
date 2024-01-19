<?php

namespace App\Candidate\InterviewManagement\Resources;

use Domain\Organization\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Organization $resource
 */
class OrganizationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'contact_email' => $this->resource->contact_email,
            'website_url' => $this->resource->website_url,
            'industry' => $this->resource->industry,
        ];
    }
}
