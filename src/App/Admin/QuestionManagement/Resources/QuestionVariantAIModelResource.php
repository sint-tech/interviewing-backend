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
        dd($this->resource);

        return [
            'name' => $this->resource->name,
            'status' => (string) $this->resource->pivot->status,
            'weight' => $this->resource->pivoit->weight,
            'system_prompt' => $this->resource->pivot->aiModelClientFactory(),
        ];
    }
}
