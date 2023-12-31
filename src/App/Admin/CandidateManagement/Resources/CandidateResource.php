<?php

namespace App\Admin\CandidateManagement\Resources;

use Domain\Candidate\Models\Candidate;
use Illuminate\Http\Resources\Json\JsonResource;
use Support\ValueObjects\DateToHumanReadValueObject;

/**
 * @property Candidate $resource
 */
class CandidateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'first_name' => (string) $this->resource->first_name,
            'last_name' => (string) $this->resource->last_name,
            'email' => (string) $this->resource->email,
            'mobile_country_code' => $this->resource->mobile_number->countryShortCode,
            'mobile_dial_code' => $this->resource->mobile_number->dialCode,
            'mobile_number' => $this->resource->mobile_number->number,
            'created_at' => (new DateToHumanReadValueObject($this->created_at))->toFullDateTimeFormat(),
        ];
    }
}
