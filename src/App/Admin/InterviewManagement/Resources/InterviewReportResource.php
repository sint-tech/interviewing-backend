<?php

namespace App\Admin\InterviewManagement\Resources;

use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Illuminate\Http\Resources\Json\JsonResource;
use Support\ValueObjects\DateToHumanReadValueObject;

/**
 * @property Interview $resource
 */
class  InterviewReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'interview' => InterviewResource::make($this->resource),
            'average_score' => (float) $this->resource->defaultLastReport->avg_score,
            'advices' => (array) $this->resource->defaultLastReport->advices,
            'impacts' => (array) $this->resource->defaultLastReport->impacts,
            'question_clusters_scores' => $this->resource->defaultLastReport->questionClustersStats,
            'created_at' => (new DateToHumanReadValueObject($this->resource->defaultLastReport->created_at))->toFullDateTimeFormat(),
        ];
    }
}
