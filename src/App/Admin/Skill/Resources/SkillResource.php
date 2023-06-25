<?php

namespace App\Admin\Skill\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'description' => (string) $this->description,
            'created_at' => (string) $this->created_at?->format('Y-m-d H:m'),
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:m')),
        ];
    }
}
