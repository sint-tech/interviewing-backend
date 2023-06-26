<?php

namespace App\Admin\QuestionManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class QuestionVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'text' => (string) $this->text,
            'description' => $this->when(! is_null($this->description), (string) $this->description),
            'question' => QuestionResource::make($this->whenLoaded('question')),
            'question_cluster_id'   => $this->relationLoaded('question') ? $this->question->question_cluster_id : new MissingValue(),
            'reading_time_in_seconds' => (int) $this->reading_time_in_seconds,
            'answering_time_in_seconds' => (int) $this->answering_time_in_seconds,
            //todo::badawy create QuestionVariantCreatorResource
            //todo::badawy create QuestionVariantOwnerResource
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i')),
        ];
    }
}
