<?php

namespace App\Candidate\Auth\Resources;

use App\Candidate\JobTitle\Resources\JobTitleResource;
use Domain\Candidate\Models\Candidate;
use Illuminate\Http\Resources\Json\JsonResource;
use Support\ValueObjects\MobileNumber;

/** @property Candidate $resource */
class CandidateResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => (int) $this->resource->id,
            'first_name' => (string) $this->resource->first_name,
            'last_name' => (string) $this->resource->last_name,
            'email' => (string) $this->resource->email,
            $this->mergeWhen($this->resource->mobile_number instanceof MobileNumber, [
                'mobile' => [
                    'country' => (string) $this->resource->mobile_number?->countryShortCode,
                    'dial_code' => (string) $this->resource->mobile_number?->dialCode,
                    'number' => (string) $this->resource->mobile_number?->number,
                ],
            ]),
            'current_job_title' => JobTitleResource::make($this->whenLoaded('currentJobTitle')),
            'desire_hiring_positions' => JobTitleResource::collection($this->whenLoaded('desireHiringPositions')),
        ];

        return $data;
    }
}
