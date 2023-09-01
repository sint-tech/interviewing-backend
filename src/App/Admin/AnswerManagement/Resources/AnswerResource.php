<?php

namespace App\Admin\AnswerManagement\Resources;

use App\Admin\QuestionManagement\Resources\QuestionVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'text' => (string) $this->text,
            'min_score' => (float) $this->min_score,
            'max_score' => (float) $this->max_score,
            'question_variant' => QuestionVariantResource::make($this->whenLoaded('questionVariant')),
        ];
    }
}
