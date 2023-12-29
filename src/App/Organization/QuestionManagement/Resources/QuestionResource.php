<?php

namespace App\Organization\QuestionManagement\Resources;

use Domain\QuestionManagement\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Question $resource
 */
class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'difficult_level' => (int) $this->resource->difficult_level,
            'min_reading_duration_in_seconds' => (int) $this->resource->min_reading_duration_in_seconds,
            'max_reading_duration_in_seconds' => (int) $this->resource->max_reading_duration_in_seconds,
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants'))
        ];
    }
}
