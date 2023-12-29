<?php

namespace App\Organization\QuestionManagement\Resources;

use App\Organization\SkillManagement\Resources\SkillResource;
use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property QuestionCluster $resource
 */
class QuestionClusterResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'name' => (string) $this->resource->name,
            'description' => (string) $this->resource->description,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants')),
            //todo add skill resource
        ];
    }
}
