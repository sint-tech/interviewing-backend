<?php

namespace App\Organization\InvitationManagement\Resources;

use App\Organization\Vacancy\Resources\VacancyResource;
use Domain\Invitation\Models\Invitation;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Invitation $resource */
class InvitationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getKey(),
            'email' => $this->resource->email,
            'batch' => $this->resource->batch,
            'mobile_number' => $this->resource->mobile_number->number,
            'mobile_country_code' => $this->resource->mobile_number->number,
            'should_be_invited_at' => $this->resource->should_be_invited_at?->format('Y-m-d H:i'),
            'last_invited_at' => $this->resource->last_invited_at?->format('Y-m-d H:i'),
            'vacancy' => VacancyResource::make($this->whenLoaded('vacancy')),
            'expired_at' => $this->resource->expired_at?->format('Y-m-d H:i'),
            'deleted_at' => $this->whenNotNull($this->resource->deleted_at?->format('Y-m-d H:i')),
        ];
    }
}
