<?php

namespace App\Organization\InterviewManagement\Resources;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Interview $resource */
class InterviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->id,
            'status' => $this->resource->status,
            'candidate' => $this->whenLoaded('candidate', function () {
                return [
                    'id' => $this->resource->candidate->id,
                    'full_name' => $this->resource->candidate->full_name,
                    'mobile_number' => $this->resource->candidate->mobile_number?->number,
                    'mobile_number_dial_code' => $this->resource->candidate->mobile_number?->dialCode,
                    'mobile_number_country_code' => $this->resource->candidate->mobile_number?->countryShortCode,
                ];
            }),
        ];
    }
}
