<?php

namespace App\Organization\Vacancy\Resources;

use App\Organization\InterviewManagement\Resources\InterviewTemplateResource;
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
            'id' => $this->resource->getKey(),
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'max_reconnection_tries' => $this->resource->max_reconnection_tries,
            'open_positions' => $this->resource->open_positions,
            'started_at' => $this->resource->started_at?->format('Y-m-d H:i'),
            'ended_at' => $this->resource->ended_at?->format('Y-m-d H:i'),
            'created_at' => $this->resource->created_at->format('Y-m-d H:i'),
            'last_updated_at' => $this->resource->updated_at->format('Y-m-d H:i'),
            'interview_template_id' => $this->resource->interview_template_id,
            'interview_template' => InterviewTemplateResource::make($this->whenLoaded('interviewTemplate')),
            'deleted_at' => $this->when(! is_null($this->deleted_at), $this->resource->deleted_at?->format('Y-m-d H:m')),
        ];
    }
}
