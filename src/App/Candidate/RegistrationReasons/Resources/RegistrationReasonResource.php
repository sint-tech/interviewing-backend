<?php

namespace App\Candidate\RegistrationReasons\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationReasonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
        ];
    }
}
