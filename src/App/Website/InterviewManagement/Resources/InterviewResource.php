<?php

namespace App\Website\InterviewManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'candidate_id'  => (int) $this->candidate_id,
            'started_at'    => (string) $this->started_at?->format('Y-m-d H:i'),
            'ended_at'      => (string) $this->ended_at?->format('Y-m-d H:i'),
            'is_ended'      => ! is_null($this->ended_at),
            'interview_answers' => AnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
