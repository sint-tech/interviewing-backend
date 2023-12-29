<?php

namespace App\Organization\InterviewManagement\Resources;

use App\Organization\QuestionManagement\Resources\QuestionVariantResource;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

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
            'job_profile_id' => $this->resource->targeted_job_title_id,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i'),
            'deleted_at' => $this->when(! is_null($this->resource->deleted_at), $this->resource->deleted_at?->format('Y-m-d H:i'), new MissingValue()),
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants')),
        ];
    }
}
