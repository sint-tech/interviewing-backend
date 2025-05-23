<?php

namespace App\Admin\QuestionManagement\Resources;

use App\Admin\Skill\Resources\SkillResource;
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
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants')),
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:m')),
        ];
    }
}
