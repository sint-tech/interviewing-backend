<?php

namespace App\Organization\QuestionManagement\Resources;

use Domain\Organization\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Organization $resource
 */
class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => (int) $this->getKey(),
            'name' => (string) $this->name,
        ];
    }
}
