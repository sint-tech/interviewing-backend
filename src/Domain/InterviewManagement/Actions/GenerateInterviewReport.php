<?php

namespace Domain\InterviewManagement\Actions;

use Domain\AiPromptMessageManagement\Enums\PromptTemplateVariableEnum;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Domain\InterviewManagement\Exceptions\InterviewNotFinishedException;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\ValueObjects\InterviewReportValueObject;
use Domain\ReportManagement\Actions\CreateReportAction;
use Domain\ReportManagement\DataTransferObjects\ReportDto;
use Domain\ReportManagement\DataTransferObjects\ReportValueDto;
use Domain\ReportManagement\Models\InterviewReport;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;
use Support\ValueObjects\PromptMessage;

class GenerateInterviewReport
{
    protected Interview $interview;

    public function __construct(
    ) {
    }

    public function execute(
        Interview $interview
    ): InterviewReportValueObject {
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
            ],
        ]);

        app(CreateReportAction::class)->execute($reportDto);

        return new InterviewReportValueObject($interview);
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
}
