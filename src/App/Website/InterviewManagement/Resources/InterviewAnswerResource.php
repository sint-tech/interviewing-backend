<?php

namespace App\Website\InterviewManagement\Resources;

use App\Admin\QuestionManagement\Resources\QuestionVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewAnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => (int) $this->id,
            'answer_text'   => (string) $this->answer_text,
            'answered_at'   => (string) $this->created_at?->format('Y-m-d H:i'),
            'question_occurrence_reason'    => (string) $this->question_occurrence_reason,
            'score'         => (float) $this->score,
            'question_variant'  => QuestionVariantResource::make(
                $this->whenLoaded('questionVariants')
            )
        ];
    }
}
