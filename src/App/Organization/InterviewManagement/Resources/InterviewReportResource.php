<?php

namespace App\Organization\InterviewManagement\Resources;

use Domain\InterviewManagement\Models\Interview;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Interview $resource */
class InterviewReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'interview' => InterviewResource::make($this->resource),
            'average_score' => (float) $this->resource->defaultLastReport->avg_score,
            'language_fluency_score' => (float) $this->resource->defaultLastReport->language_fluency_score,
            'advices' => (array) $this->resource->defaultLastReport->recruiter_advices,
            'impacts' => (array) $this->resource->defaultLastReport->impacts,
            'question_clusters_scores' => $this->resource->defaultLastReport->question_clusters_stats,
            'emotional_score' => $this->resource->defaultLastReport->emotional_score,
            'created_at' => $this->resource->defaultLastReport->created_at?->format('Y-m-d H:i'),
        ];
    }
}
