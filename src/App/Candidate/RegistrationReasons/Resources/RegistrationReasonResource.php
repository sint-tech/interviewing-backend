<?php

namespace App\Candidate\RegistrationReasons\Resources;

use Domain\Candidate\Models\RegistrationReason;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property RegistrationReason $resource
 */
class RegistrationReasonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->id,
            'name' => (string) $this->resource->name,
        ];
    }
}
