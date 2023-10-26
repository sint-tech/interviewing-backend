<?php

namespace App\Admin\AIModelManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AIModelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
        ];
    }
}
