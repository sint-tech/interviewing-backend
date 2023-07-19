<?php

namespace Domain\InterviewManagement\ValueObjects;

use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Contracts\Support\Arrayable;

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
        if ($this->interviewStillRunning()) {
            throw new InterviewNotFinishedException();
        }

        $this->candidate = $this->interview->candidate;

        $this->avgScore = $this->interview->answers->avg(fn($answer) => $answer->score /$answer->max_score) * 100;

        $this->setRecommendations();

        $this->questionClustersStats();
    }

    public function interviewStillRunning():bool
    {
        return is_null($this->interview->ended_at);
    }

    private function setRecommendations(): void
    {
        $this->advices = $this->interview->answers->map(fn(Answer $answer) => $answer->advice_statement)->filter()->toArray();

        $this->impacts = $this->interview->answers->map(fn(Answer $answer) => $answer->impact_statement)->filter()->toArray();
    }

    public function questionClustersStats():void
    {
        $clustersStats = [];

        foreach ($this->interview->answers as $answer) {
            if (is_null($answer->questionCluster)) {
                continue;
            }

            $clustersStats[$answer->question_cluster_id] = [
                'total_scores' => + ($answer->score / $answer->max_score),
                'name'  => $answer->questionCluster->name,
                'description'  => $answer->questionCluster->description,
            ];
        }

        array_walk($clustersStats,function (&$question_cluster) {
            $question_cluster['avg_score'] = $question_cluster['total_scores'] * 100;
            unset($question_cluster['total_scores']);
        });

        $this->questionClustersStats = $clustersStats;
    }
}
