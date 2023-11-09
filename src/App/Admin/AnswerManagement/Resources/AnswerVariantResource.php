<?php

namespace App\Admin\AnswerManagement\Resources;

use App\Admin\Organization\Resources\OrganizationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'text' => (string) $this->text,
            'description' => (string) $this->description,
            'score' => (float) $this->score,
            'answer' => AnswerResource::make($this->whenLoaded('answer')),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
        ];
    }
}
