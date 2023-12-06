<?php

namespace App\Admin\QuestionManagement\Resources;

use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AIPrompt $resource
 */
class QuestionVariantAIPromptResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->resource->model,
            'status' => $this->resource->status,
            'weight' => $this->resource->weight,
            'system_prompt' => (string) $this->resource->system_prompt,
            'content_prompt' => (string) $this->resource->content_prompt,
        ];
    }
}
