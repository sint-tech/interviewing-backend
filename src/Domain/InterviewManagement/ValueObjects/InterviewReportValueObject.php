<?php

namespace Domain\InterviewManagement\ValueObjects;

use App\Candidate\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Actions\GenerateInterviewReport;
use Domain\InterviewManagement\Models\Interview;
use LogicException;

class InterviewReportValueObject //todo rename the value object to DefaultInterviewReportForCandidateValueObject
{
    public readonly Candidate $candidate;

    public readonly float $avgScore;

    public readonly array $advices;

    public readonly array $impacts;

    public readonly array $questionClustersStats;

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

        $this->advices = $report_values['advices'];

        $this->impacts = $report_values['impacts'];
    }
}
