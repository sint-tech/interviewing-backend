<?php

namespace App\Admin\Organization\Resources;

use Domain\Organization\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id'            => (int) $this->getKey(),
            'name'      => (string) $this->name,
            'created_at'    => (string) $this->created_at->format('Y-m-d'),
            'deleted_at'    => $this->when(! is_null($this->deleted_at),(string) $this->deleted_at?->format('Y-m-d H:i')),
            'employees'     => EmployeeResource::collection($this->whenLoaded('employees')),
            'current_manager'       => EmployeeResource::make($this->whenLoaded('oldestManager'))
        ];
    }
}
