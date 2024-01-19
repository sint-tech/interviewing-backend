<?php

namespace App\Candidate\InterviewManagement\Resources;

use Domain\Candidate\Models\Candidate;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Candidate $resource
 */
class CandidateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => (string) $this->full_name,
            'email' => (string) $this->email,
            'mobile' => [
                'dial_code' => $this->resource->mobile_number->dialCode,
                'country' => $this->resource->mobile_number->countryShortCode,
                'number' => $this->resource->mobile_number->number,
            ],
        ];
    }
}
