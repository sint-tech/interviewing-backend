<?php

namespace App\Organization\EmployeeManagement\Resource;

use Domain\Organization\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Employee $resource
 */
class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'deleted_at' => $this->whenNotNull($this->resource->deleted_at?->toDateTimeString()),
        ];
    }
}
