<?php

namespace App\Candidate\QuestionManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionClusterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'description' => (string) $this->description,
            'created_at' => (string) $this->created_at?->format('Y-m-d H:m'),
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants')),
        ];
    }
}
