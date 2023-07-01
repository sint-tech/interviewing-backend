<?php

namespace App\Website\QuestionManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class QuestionVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'text' => (string) $this->text,
            'description' => $this->description,
            'reading_time_in_seconds' => (int) $this->reading_time_in_seconds,
            'answering_time_in_seconds' => (int) $this->answering_time_in_seconds,
            'default_ai_prompt_message' => AiPromptMessageResource::make($this->whenLoaded('defaultAiPromptMessage')),
        ];
    }
}
