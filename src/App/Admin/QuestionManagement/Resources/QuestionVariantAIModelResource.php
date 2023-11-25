<?php

namespace App\Admin\QuestionManagement\Resources;

use Domain\AiPromptMessageManagement\Models\AIModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AIModel $resource
 */
class QuestionVariantAIModelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->resource->name,
            'status' =>  $this->resource->prompt_message->status,
            'weight' => $this->resource->prompt_message->weight,
            'system_prompt' => $this->resource->prompt_message->system_prompt,
            'content_prompt' => $this->resource->prompt_message->content_prompt,
        ];
    }
}
