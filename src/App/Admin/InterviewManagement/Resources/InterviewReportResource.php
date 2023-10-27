<?php

namespace App\Admin\InterviewManagement\Resources;

use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Illuminate\Http\Resources\Json\JsonResource;
use Support\ValueObjects\DateToHumanReadValueObject;

/**
 * @property InterviewReportValueObject $resource
 */
class InterviewReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'interview' => InterviewResource::make($this->resource->interview),
            'average_score' => (float) $this->resource->avgScore,
            'advices' => (array) $this->resource->advices,
            'impacts' => (array) $this->resource->impacts,
            'question_clusters_scores' => $this->resource->questionClustersStats,
            'created_at' => (new DateToHumanReadValueObject($this->resource->created_at))->toFullDateTimeFormat(),
        ];
    }
}
