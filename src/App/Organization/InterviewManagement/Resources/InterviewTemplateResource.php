<?php

namespace App\Organization\InterviewManagement\Resources;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property InterviewTemplate $resource
 */
class InterviewTemplateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'availability_status' => $this->resource->availability_status,
            'is_reusable' => $this->resource->reusable,
        ];
    }
}
