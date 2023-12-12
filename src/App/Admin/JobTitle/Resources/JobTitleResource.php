<?php

namespace App\Admin\JobTitle\Resources;

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
            'id' => (int) $this->resource->getKey(),
            'title' => (string) $this->resource->title,
            'description' => (string) $this->resource->description,
            'availability_status' => $this->resource->availability_status,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i'),
        ];
    }
}
