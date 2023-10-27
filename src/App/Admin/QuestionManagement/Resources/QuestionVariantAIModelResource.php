<?php

namespace App\Admin\QuestionManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionVariantAIModelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'status' => (string) $this->status,
            'is_default' => (bool) $this->pivot->is_default,
        ];
    }
}
