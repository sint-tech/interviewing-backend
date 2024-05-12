<?php

namespace App\Admin\Organization\Resources;

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
            'website_url' => $this->resource->website_url,
            'contact_email' => $this->resource->contact_email,
            'address' => $this->resource->address,
            'number_of_employees' => $this->resource->number_of_employees,
            'industry' => $this->resource->industry,
            'created_at' => (string) $this->created_at->format('Y-m-d'),
            'deleted_at' => $this->when(! is_null($this->deleted_at), (string) $this->deleted_at?->format('Y-m-d H:i')),
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'current_manager' => EmployeeResource::make($this->whenLoaded('oldestManager')),
            'logo' => $this->whenLoaded('logo', $this->logo?->getFullUrl()),
            'limit' => $this->resource->limit,
            'interview_consumption' => $this->resource->interview_consumption,
            'limit_exceeded' => $this->limitExceeded(),
        ];
    }
}
