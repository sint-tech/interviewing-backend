<?php

namespace App\Admin\AIModelManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;

/**
 * @property PromptTemplate $resource
 */
class PromptTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => (string) $this->resource->name,
            'text' => (string) $this->resource->text,
            'stats_text' => (string) $this->resource->stats_text,
            'conclusion_text' => (string) $this->resource->conclusion_text,
            'is_selected' => (bool) $this->resource->is_selected,
            'version' => (int) $this->resource->version,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i'),
        ];
    }
}
