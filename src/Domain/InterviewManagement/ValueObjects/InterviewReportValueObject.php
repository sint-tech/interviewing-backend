<?php

namespace Domain\InterviewManagement\ValueObjects;

use App\Candidate\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Carbon\Carbon;
use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Contracts\Support\Arrayable;

class InterviewReportValueObject implements Arrayable //todo rename the value object to DefaultInterviewReportForCandidateValueObject
{
    public readonly Candidate $candidate;

    public readonly float $avgScore;

    public readonly array $advices;

    public readonly array $impacts;

    public readonly array $questionClustersStats;

    public readonly Carbon $created_at;

    public function __construct(
        public readonly Interview $interview
    ) {
        $this->candidate = $this->interview->candidate;

        $report = $this->interview->defaultLastReport;

        if (is_null($report)) {
            throw new InterviewNotFinishedException();
        }

        $report_values = $report->getMeta()->toArray();

        $this->avgScore = $report_values['avg_score'];

        $this->questionClustersStats = $report_values['question_clusters_stats'];

        $this->advices = $report_values['candidate_advices'];

        $this->impacts = $report_values['impacts'];

        $this->created_at = $report->created_at;
    }

    /**
     * get the all public props for the value object as array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
