<?php

namespace Domain\InterviewManagement\ValueObjects;

use Domain\Candidate\Models\Candidate;
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
        if ($this->interviewStillRunning()) {
            throw new InterviewNotFinishedException();
        }

        $this->candidate = $this->interview->candidate;

        $this->avgScore = $this->interview->answers->avg(fn($answer) => $answer->score /$answer->max_score) * 100;

        $this->questionClustersStats();

        $this->setRecommendations();
    }

    public function interviewStillRunning():bool
    {
        return is_null($this->interview->ended_at);
    }

    private function setRecommendations(): void
    {
        $this->advices = $this->interview->answers->map(fn(Answer $answer) => $answer->advice_statement)->filter()->toArray();

        $this->impacts = Arr::wrap($this->setImpacts());
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

    private function setImpacts():string
    {
        $userContent = 'I got \\n';

        foreach ($this->questionClustersStats as $questionClustersStat) {
            $userContent .= "{$questionClustersStat['avg_score']} In {$questionClustersStat['name']} \\n";
        }

        $userContent .= "please demonstrate the impact of these scores on my career and professional life in 4 lines, without mentioning my the scores";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages'   => [
                [
                    'role'  => 'system',
                    'content'   => "I'm in an interview, and I was asked the about some questions about my behavior, then I was rated from 1 to 100 percent, in multiple fields based on my answers",
                ],
                [
                    'role' => 'user',
                    'content' => $userContent
                ]
            ]
        ]);

        return (string) ($response->choices[0])->message->content;
    }
}
