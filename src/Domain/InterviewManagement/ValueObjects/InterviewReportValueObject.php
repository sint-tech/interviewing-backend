<?php

namespace Domain\InterviewManagement\ValueObjects;

use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Actions\GenerateInterviewReport;
use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class InterviewReportValueObject
{
    public readonly Candidate $candidate;

    public readonly float $avgScore;

    public readonly array $advices;

    public readonly array $impacts;

    public readonly array $questionClustersStats;

    public function __construct(
        public readonly Interview $interview
    )
    {
        $this->candidate = $this->interview->candidate;

        (new GenerateInterviewReport($this->interview))->execute();

        $report = $this->interview->latestReport;

        $report_values = $report->getMeta()->toArray();

        $this->avgScore = $report_values['avg_score'];

        $this->questionClustersStats = $report_values['question_clusters_stats'];

        $this->advices = $report_values['advices'];

        $this->impacts = $report_values['impacts'];
    }
}
