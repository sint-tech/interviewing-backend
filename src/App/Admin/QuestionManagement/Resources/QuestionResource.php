<?php

namespace App\Admin\QuestionManagement\Resources;

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
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'description' => (string) $this->description,
            'question_type' => (string) $this->question_type,
            'difficult_level' => (int) $this->difficult_level,
            'min_reading_duration_in_seconds' => (int) $this->min_reading_duration_in_seconds,
            'max_reading_duration_in_seconds' => (int) $this->max_reading_duration_in_seconds,
            'default_ai_prompt' => QuestionVariantAIPromptResource::make($this->whenLoaded('defaultAIPrompt')),
            'question_cluster' => QuestionClusterResource::make($this->whenLoaded('questionCluster')),
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants')),
            'created_at' => $this->created_at?->format('Y-m-d H:m'),
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:m')),
        ];
    }
}
