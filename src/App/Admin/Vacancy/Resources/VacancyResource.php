<?php

namespace App\Admin\Vacancy\Resources;

use App\Admin\InterviewManagement\Resources\InterviewTemplateResource;
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
            'started_at' => $this->resource->started_at?->format('Y-m-d H:m'),
            'ended_at' => $this->resource->ended_at?->format('Y-m-d H:m'),
            'created_at' => $this->resource->created_at->format('Y-m-d H:m'),
            'last_updated_at' => $this->resource->updated_at->format('Y-m-d H:m'),
            'deleted_at' => $this->when(! is_null($this->deleted_at), $this->resource->deleted_at?->format('Y-m-d H:m')),
            'default_interview_template' => InterviewTemplateResource::make($this->whenLoaded('defaultInterviewTemplate')),
        ];
    }
}
