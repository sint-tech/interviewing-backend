<?php

namespace App\Candidate\QuestionManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AiPromptMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'message' => (string) $this->prompt_text,
            'ai_model' => (string) $this->aiModel->name->value,
            'is_default' => (bool) $this->is_default,
        ];
    }
}
