<?php

namespace App\Candidate\InterviewManagement\Resources;

use App\Candidate\QuestionManagement\Resources\QuestionClusterResource;
use App\Candidate\QuestionManagement\Resources\QuestionVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StartedInterviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'started_at' => (string) $this->started_at->format('Y-m-d H:i'),
            'ended_at' => (string) $this->ended_at?->format('Y-m-d H:i'),
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants')),
        ];
    }
}
