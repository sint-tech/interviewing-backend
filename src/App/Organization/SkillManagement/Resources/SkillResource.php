<?php

namespace App\Organization\SkillManagement\Resources;

use Domain\Skill\Models\Skill;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Skill $resource
 */
class SkillResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'name' => (string) $this->resource->name,
            'description' => $this->resource->description,
        ];
    }
}
