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
            'advices' => (array) $this->resource->advices,
            'impacts' => (array) $this->resource->impacts,
            'question_clusters_scores' => $this->resource->question_clusters_stats,
            'candidate' => CandidateResource::make($this->resource->reportable->candidate),
            'vacancy' => VacancyResource::make($this->resource->reportable->vacancy),
            //creation_at
            //interview_id
            //interview_candidate_name
        ];
    }
}
