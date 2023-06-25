<?php

namespace App\Website\InterviewManagement\Resources;

use App\Admin\QuestionManagement\Resources\QuestionVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StartedInterviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => (int) $this->id,
            'started_at'    => (string) $this->started_at->format('Y-m-d H:i'),
            'ended_at'      => (string) $this->ended_at?->format('Y-m-d H:i'),
            'questions_variants'     => QuestionVariantResource::collection($this->questionVariants),
            //todo::badawy create custom question_Variants resource
        ];
    }
}
