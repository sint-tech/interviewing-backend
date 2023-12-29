<?php

namespace App\Organization\QuestionManagement\Resources;

use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property QuestionVariant $resource
 */
class QuestionVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getKey(),
            'text' => $this->resource->text,
            'description' => $this->resource->description,
            'reading_time_in_seconds' => $this->resource->reading_time_in_seconds,
            'answering_time_in_seconds' => $this->resource->answering_time_in_seconds,
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i')),
            'question_id' => $this->resource->question_id,
        ];
    }
}
