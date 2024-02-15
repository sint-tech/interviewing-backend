<?php

namespace App\Admin\Skill\Resources;

use App\Admin\QuestionManagement\Resources\QuestionResource;
use App\Admin\QuestionManagement\Resources\QuestionVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillQuestionClustersResource extends JsonResource
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
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:m')),
        ];
    }
}
