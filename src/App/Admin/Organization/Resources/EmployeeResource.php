<?php

namespace App\Admin\Organization\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->getKey(),
            'first_name' => (string) $this->first_name,
            'last_name' => (string) $this->last_name,
            'email' => (string) $this->email,
            'organization_id' => (int) $this->organization_id,
            'is_manager' => (bool) $this->is_organization_manager,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i')),
        ];
    }
}
