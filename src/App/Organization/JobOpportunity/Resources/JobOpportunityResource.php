<?php

namespace App\Organization\JobOpportunity\Resources;

use Domain\Vacancy\Models\JobOpportunity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property JobOpportunity $resource
 */
class JobOpportunityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getKey(),
            'title' => $this->resource->title,
            //todo add keys for this resource
        ];
    }
}
