<?php

namespace App\Candidate\InterviewManagement\Resources;

use Domain\Vacancy\Models\Vacancy;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Vacancy $resource
 */
class VacancyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->descripion,
            'interview_template_id' => $this->resource->interview_template_id,
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
        ];
    }
}
