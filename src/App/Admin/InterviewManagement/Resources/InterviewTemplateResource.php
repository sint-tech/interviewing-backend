<?php

namespace App\Admin\InterviewManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterviewTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
          'id'      => (int) $this->id,
          'name'        => (string) $this->name,
          'description'    => (string) $this->description,
          'reusable'        => (bool)  $this->reusable,
          'availability_status' => $this->availability_status,
        ];
    }
}
