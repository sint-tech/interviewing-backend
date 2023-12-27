<?php

namespace App\Organization\QuestionManagement\Resources;

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
            //todo add skill resource
        ];
    }
}
