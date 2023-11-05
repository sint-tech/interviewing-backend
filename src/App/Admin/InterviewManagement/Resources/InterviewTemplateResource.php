<?php

namespace App\Admin\InterviewManagement\Resources;

use App\Admin\QuestionManagement\Resources\QuestionVariantResource;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property InterviewTemplate $resource
 */
class InterviewTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'name' => (string) $this->resource->name,
            'description' => (string) $this->resource->description,
            'reusable' => (bool) $this->resource->reusable,
            'availability_status' => $this->resource->availability_status,
            'question_variants' => QuestionVariantResource::collection($this->whenLoaded('questionVariants')),
        ];
    }
}
