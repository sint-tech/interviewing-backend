<?php

namespace App\Organization\JobTitle\Resources;

use Domain\JobTitle\Models\JobTitle;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property JobTitle $resource
 */
class JobTitleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'title' => $this->resource->title,
            'description' => $this->resource->description,
        ];
    }
}
