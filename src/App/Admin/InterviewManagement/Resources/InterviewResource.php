<?php

namespace App\Admin\InterviewManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => (int) $this->getKey(),
            'status'    => $this->status,
            'candidate' =>  InterviewCandidateResource::make($this->whenLoaded('candidate')),
        ];
    }
}
