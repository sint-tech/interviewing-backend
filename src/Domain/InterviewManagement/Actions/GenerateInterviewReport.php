<?php

namespace Domain\InterviewManagement\Actions;

use Domain\AiPromptMessageManagement\Enums\PromptTemplateVariableEnum;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\InterviewManagement\Models\Interview;
use Domain\ReportManagement\Actions\CreateReportAction;
use Domain\ReportManagement\DataTransferObjects\ReportDto;
use Domain\ReportManagement\DataTransferObjects\ReportValueDto;
use Domain\ReportManagement\Models\InterviewReport;
use Domain\ReportManagement\Models\Report;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateInterviewReport
{
    protected Interview $interview;

    public function __construct(
    ) {
    }

    /**
     * @throws InterviewNotFinishedException
     */
    public function execute(
        Interview $interview
    ):Report {
        if ($this->interviewStillRunning($interview)) {
            throw new InterviewNotFinishedException();
        }

        $this->interview = $interview;

        $reportDto = ReportDto::from([
            'name' => InterviewReport::DEFAULT_REPORT_NAME,
            'reportable' => $interview,
            'values' => [
                ReportValueDto::from([
                    'key' => 'avg_score',
                    'value' => $interview->answers->avg(fn ($answer) => $answer->score / $answer->max_score) * 100,
                ]),
                ReportValueDto::from([
                    'key' => 'impacts',
                    'value' => $this->getRecommendations($interview, 'impacts'),
                ]),
                ReportValueDto::from([
                    'key' => 'candidate_advices',
                    'value' => $this->getRecommendations($interview, 'candidate_advices'),
                ]),
                ReportValueDto::from([
                    'key' => 'question_clusters_stats',
                    'value' => $this->getQuestionClustersStats($interview),
                ]),
                ReportValueDto::from([
                    'key' => 'language_fluency_score',
                    'value' => $interview->answers->avg(fn ($answer) => $answer->english_score / 10) * 100,
                ]),
                ReportValueDto::from([
                    'key' => 'recruiter_advices',
                    'value' => $this->getRecommendations($interview, 'recruiter_advices'),
                ]),
                ReportValueDto::from([
                    'key' => 'emotional_score',
                    'value' => $this->getAverageEmotionalScore($interview),
                ])
            ],
        ]);

        app(CreateReportAction::class)->execute($reportDto);

        return $interview->refresh()->defaultLastReport;
    }

    public function interviewStillRunning(Interview $interview): bool
    {
        return is_null($interview->ended_at);
    }

    private function getRecommendations(Interview $interview, string $recommendation_type): array
    {
        $prompt_template = PromptTemplate::query()
            ->latestTemplateOr(
                $recommendation_type,
                fn() => abort(404, 'no active prompt template with name: '. $recommendation_type)
            );

        //todo use @PromptMessage valueObject
        $template_content = str_replace(
            [PromptTemplateVariableEnum::JobTitle->value],
            [$this->interview->vacancy->interviewTemplate->jobTitle->title],
            $prompt_template->text
        );

        foreach ($this->getQuestionClustersStats($interview) as $questionClustersStat) {
            $template_content .= str_replace([
                PromptTemplateVariableEnum::QuestionClusterName->value,
                PromptTemplateVariableEnum::QuestionClusterAvgScore->value,
            ], [
                $questionClustersStat['name'],
                $questionClustersStat['avg_score'],
            ], $prompt_template->stats_text);
        }

        $template_content .= $prompt_template->conclusion_text;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $template_content,
                ],
            ],
        ]);

        return Arr::wrap((string) ($response->choices[0])->message->content);
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

    private function getAverageEmotionalScore(Interview $interview): array
    {
        if ($interview->answers->contains(fn ($answer) => $answer->ml_video_semantics === null)) {
            return [];
        }

        $count = $interview->answers->count();

        $emotions = $interview->answers
            ->map(fn ($answer) => (array) json_decode($answer->ml_video_semantics)->emotions)
            ->reduce(function ($carry, $emotions) {
                foreach ($emotions as $emotion => $value) {
                    $carry[$emotion] = ($carry[$emotion] ?? 0) + (int) $value;
                }

                return $carry;
            }, []);

        $averageEmotions = collect($emotions)->map(fn ($value) => $value / $count);

        return $averageEmotions->sortDesc()->take(5)->toArray();
    }
}
