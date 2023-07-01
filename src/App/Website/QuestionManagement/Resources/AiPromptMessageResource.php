<?php

namespace App\Website\QuestionManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AiPromptMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'  => (int) $this->id,
            'message'  => (string) $this->prompt_text,
            'ai_model'        => (string) $this->ai_model->value,
            'is_default'      => (boolean) $this->is_default,
        ];
    }
}
