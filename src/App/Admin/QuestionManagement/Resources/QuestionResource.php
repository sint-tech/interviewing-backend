<?php

namespace App\Admin\QuestionManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'description' => (string) $this->description,
            'question_type' => (string) $this->question_type,
            'difficult_level' => (int) $this->difficult_level,
            'min_reading_duration_in_seconds' => (int) $this->min_reading_duration_in_seconds,
            'max_reading_duration_in_seconds' => (int) $this->max_reading_duration_in_seconds,
            'question_cluster' => QuestionClusterResource::make($this->whenLoaded('questionCluster')),
            'created_at' => $this->created_at?->format('Y-m-d H:m'),
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('y-m-d H:m')),
        ];
    }
}
