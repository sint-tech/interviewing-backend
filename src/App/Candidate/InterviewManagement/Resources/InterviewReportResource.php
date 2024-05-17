<?php

namespace App\Candidate\InterviewManagement\Resources;

use Domain\ReportManagement\Models\InterviewReport;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property InterviewReport $resource
 */
class InterviewReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'interview_id' => $this->resource->reportable_id,
            'average_score' => (float) $this->resource->avg_score,
            'language_fluency_score' => (float) $this->resource->language_fluency_score,
            'advices' => (array) $this->resource->candidate_advices,
            'impacts' => (array) $this->resource->impacts,
            'question_clusters_scores' => $this->resource->question_clusters_stats,
            'emotional_score' => $this->resource->emotional_score,
            'candidate' => CandidateResource::make(auth()->user()),
            'vacancy' => VacancyResource::make($this->resource->reportable->vacancy),
            'created_at' => $this->resource->created_at->format('Y-m-d H:i'),
            //interview_id
            //interview_candidate_name
        ];
    }
}
