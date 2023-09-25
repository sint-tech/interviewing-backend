<?php

namespace App\Admin\InterviewManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterviewCandidateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'full_name' => "$this->first_name $this->last_name",
            'email' => (string) $this->email,
            'mobile_country' => (string) $this->mobile_country,
            'mobile_number' => (string) $this->mobile_number,
        ];
    }
}
