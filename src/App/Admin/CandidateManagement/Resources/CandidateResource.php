<?php

namespace App\Admin\CandidateManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'first_name' => (string) $this->first_name,
            'last_name' => (string) $this->last_name,
            'email' => (string) $this->email,
            'mobile_country' => (string) $this->mobile_country,
            //            'mobile_country_code' => (string) $this->mobile_country_code,
            'mobile_number' => (string) $this->mobile_number,
        ];
    }
}
