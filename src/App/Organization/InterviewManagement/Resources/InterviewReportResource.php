<?php

namespace App\Organization\InterviewManagement\Resources;

use App\Admin\InterviewManagement\Resources\InterviewResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'interview' => InterviewResource::make($this->resource), //todo make interview resource for candidate app
            'average_score' => (float) $this->resource->defaultLastReport->avg_score,
            'advices' => (array) $this->resource->defaultLastReport->advices,
            'impacts' => (array) $this->resource->defaultLastReport->impacts,
            'question_clusters_scores' => $this->resource->defaultLastReport->questionClustersStats,
            'created_at' => $this->resource->defaultLastReport->created_at?->format('Y-m-d H:i'),
        ];
    }
}
