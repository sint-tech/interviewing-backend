<?php

namespace App\Website\JobTitle\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobTitleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"            => (int) $this->id,
            "title"         => (string) $this->title,
            "description"   => (string) $this->description,
            "status"        => $this->availability_status->value
        ];
    }
}
