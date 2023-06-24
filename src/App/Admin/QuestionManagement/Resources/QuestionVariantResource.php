<?php

namespace App\Admin\QuestionManagement\Resources;

use App\Website\Auth\Resources\CandidateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => (int) $this->id,
            'text'  => (string) $this->text,
            'description'   => $this->when(! is_null($this->description),(string) $this->description),
            'question'      => QuestionResource::make($this->whenLoaded('question')),
            'reading_time_in_seconds'   => (int) $this->reading_time_in_seconds,
            'answering_time_in_seconds' => (int) $this->answering_time_in_seconds,
//            'creator',
//            'owner'
        //todo::badawy create QuestionVariantCreatorResource
        //todo::badawy create QuestionVariantOwnerResource
            'deleted_at'    => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i')),
        ];
    }
}
