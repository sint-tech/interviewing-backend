<?php

namespace App\Candidate\InterviewManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'full_name' => (string) $this->full_name,
            'email' => (string) $this->email,
            'mobile' => [
                'country' => (string) $this->mobile_country,
                'number' => (string) $this->mobile_number,
            ]
        ];
    }
}
