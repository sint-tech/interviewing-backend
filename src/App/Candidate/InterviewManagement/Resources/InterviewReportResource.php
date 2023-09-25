<?php

namespace App\Candidate\InterviewManagement\Resources;

use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewReportResource extends JsonResource
{
    public $resource = InterviewReportValueObject::class;

    public function toArray($request)
    {
        return [
            'interview' => InterviewResource::make($this->interview),
            'average_score' => (float) $this->avgScore,
            'advices' => (array) $this->advices,
            'impacts' => (array) $this->impacts,
            'question_clusters_scores' => $this->questionClustersStats,
            //creation_at
            //interview_id
            //interview_candidate_name
        ];
    }
}
