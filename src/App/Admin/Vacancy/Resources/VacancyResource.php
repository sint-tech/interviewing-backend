<?php

namespace App\Admin\Vacancy\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacancyResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
