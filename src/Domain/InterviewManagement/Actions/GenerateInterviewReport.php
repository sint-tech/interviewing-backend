<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Domain\ReportManagement\Actions\CreateReportAction;
use Domain\ReportManagement\DataTransferObjects\ReportDto;
use Domain\ReportManagement\DataTransferObjects\ReportValueDto;
use Domain\ReportManagement\Models\Report;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateInterviewReport
{
    public function __construct(
    ) {
    }

    public function execute(
        Interview $interview
    ): InterviewReportValueObject
    {
        if ($this->interviewStillRunning($interview)) {
            throw new InterviewNotFinishedException();
        }

        $reportDto = ReportDto::from([
            'name' => Interview::DEFAULT_REPORT_NAME,
            'reportable' => $interview,
            'values' => [
                ReportValueDto::from([
                    'key' => 'avg_score',
                    'value' => $interview->answers->avg(fn ($answer) => $answer->score / $answer->max_score) * 100,
                ]),
                ReportValueDto::from([
                    'key' => 'impacts',
                    'value' => $this->getRecommendations($interview,'impacts'),
                ]),
                ReportValueDto::from([
                    'key' => 'advices',
                    'value' => $this->getRecommendations($interview,'advices'),
                ]),
                ReportValueDto::from([
                    'key' => 'question_clusters_stats',
                    'value' => $this->getQuestionClustersStats($interview),
                ]),
            ],
        ]);

        app(CreateReportAction::class)->execute($reportDto);

        return (new InterviewReportValueObject($interview));
    }

    public function interviewStillRunning(Interview $interview): bool
    {
        return is_null($interview->ended_at);
    }

    private function getRecommendations(Interview $interview,string $recommendation_type = 'advices' | 'impacts'): array
    {
        return match ($recommendation_type) {
            'advices' => $interview->answers->map(fn (Answer $answer) => $answer->advice_statement)->filter()->toArray(),
            'impacts' => Arr::wrap($this->getImpacts($this->getQuestionClustersStats($interview))),
        };
    }

    public function getQuestionClustersStats(Interview $interview): array
    {
        $clustersStats = [];

        foreach ($interview->answers as $answer) {
            if (is_null($answer->questionCluster)) {
                continue;
            }

            $clustersStats[$answer->question_cluster_id] = [
                'total_scores' => +($answer->score / $answer->max_score),
                'name' => $answer->questionCluster->name,
                'description' => $answer->questionCluster->description,
            ];
        }

        array_walk($clustersStats, function (&$question_cluster) {
            $question_cluster['avg_score'] = $question_cluster['total_scores'] * 100;
            unset($question_cluster['total_scores']);
        });

        return $clustersStats;
    }

    private function getImpacts(array $questionClusters): string
    {
        $userContent = 'I got \\n';

        foreach ($questionClusters as $questionClustersStat) {
            $userContent .= "{$questionClustersStat['avg_score']} In {$questionClustersStat['name']} \\n";
        }

        $userContent .= 'please demonstrate the impact of these scores on my career and professional life in 4 lines, without mentioning my the scores';

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "I'm in an interview, and I was asked the about some questions about my behavior, then I was rated from 1 to 100 percent, in multiple fields based on my answers",
                ],
                [
                    'role' => 'user',
                    'content' => $userContent,
                ],
            ],
        ]);

        return (string) ($response->choices[0])->message->content;
    }
}
