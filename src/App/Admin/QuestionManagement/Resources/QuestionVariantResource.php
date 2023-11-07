<?php

namespace App\Admin\QuestionManagement\Resources;

use App\Admin\Organization\Resources\OrganizationResource;
use App\Admin\QuestionManagement\Resources\QuestionVariantAIModelResource as AIModelResource;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * @property QuestionVariant $resource
 */
class QuestionVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'text' => (string) $this->text,
            'description' => $this->when(! is_null($this->description), (string) $this->description),
            'question' => QuestionResource::make($this->whenLoaded('question')),
            'question_cluster_id' => $this->relationLoaded('question') ? $this->question->question_cluster_id : new MissingValue(),
            'reading_time_in_seconds' => (int) $this->reading_time_in_seconds,
            'answering_time_in_seconds' => (int) $this->answering_time_in_seconds,
            'organization_id' => $this->resource->organization_id,
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'ai_models' => AIModelResource::collection($this->whenLoaded('aiModels')),
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i')),
        ];
    }
}
