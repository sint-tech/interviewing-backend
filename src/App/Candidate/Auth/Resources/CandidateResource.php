<?php

namespace App\Candidate\Auth\Resources;

use App\Candidate\JobTitle\Resources\JobTitleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'first_name' => (string) $this->first_name,
            'last_name' => (string) $this->last_name,
            'full_name' => (string) $this->full_name,
            'email' => (string) $this->email,
            'mobile' => [
                'country' => (string) $this->mobile_country,
                'number' => (string) $this->mobile_number,
            ],
            'current_job_title' => JobTitleResource::make($this->whenLoaded('currentJobTitle')),
            'desire_hiring_positions' => JobTitleResource::collection($this->whenLoaded('desireHiringPositions')),
        ];
    }
}
