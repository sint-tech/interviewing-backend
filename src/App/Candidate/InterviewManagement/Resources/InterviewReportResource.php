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
            'average_score' => (float) $this->avg_score,
            'advices' => (array) $this->advices,
            'impacts' => (array) $this->impacts,
            'question_clusters_scores' => $this->question_clusters_stats,
            'candidate' => CandidateResource::make(auth()->user()),
            //creation_at
            //interview_id
            //interview_candidate_name
        ];
    }
}
