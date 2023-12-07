<?php

namespace App\Candidate\QuestionManagement\Resources;

use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AIPrompt $resource
 */
class AiPromptMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'model' => $this->resource->model,
            'content' => $this->resource->content,
            'system' => $this->resource->system,
            'content_prompt' => (string) $this->resource->content_prompt,
            'system_prompt' => (string) $this->resource->system_prompt,
        ];
    }
}
