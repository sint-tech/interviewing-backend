<?php

namespace App\Candidate\Invitation\Resources;

use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Invitation $resource
 */
class InvitationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'is_expired' => $this->resource->is_expired,
            'vacancy_id' => $this->resource->vacancy_id,
            'last_invited_at' => $this->resource->last_invited_at?->format('Y-m-d H:i'),
        ];
    }
}
