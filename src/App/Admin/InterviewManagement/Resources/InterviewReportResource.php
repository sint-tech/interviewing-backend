<?php

namespace App\Admin\InterviewManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Support\ValueObjects\DateToHumanReadValueObject;

class InterviewReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'interview'  => InterviewResource::make($this->interview),
            'average_score' => (float) $this->avgScore,
            'advices' => (array) $this->advices,
            'impacts' => (array) $this->impacts,
            'question_clusters_scores' => $this->questionClustersStats,
            'created_at'    => (new DateToHumanReadValueObject($this->created_at))->toFullDateTimeFormat()
        ];
    }
}
